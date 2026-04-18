<script>
    window.openImportModal = function() {
        document.getElementById('importModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        feather.replace();
    };

    window.closeImportModal = function() {
        document.getElementById('importModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
        window.backToUpload();
    };

    window.backToUpload = function() {
        document.getElementById('importStepUpload').classList.remove('hidden');
        document.getElementById('importStepPreview').classList.add('hidden');
    };

    document.getElementById('file-upload')?.addEventListener('change', function(e) {
        const fileName = e.target.files[0]?.name;
        if (fileName) {
            document.getElementById('file-name-display').textContent = 'Selected: ' + fileName;
        }
    });

    document.getElementById('uploadForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const btn = document.getElementById('btnPreview');
        const spinning = document.getElementById('previewLoading');
        const text = document.getElementById('previewText');

        btn.disabled = true;
        spinning.classList.remove('hidden');
        text.textContent = 'Parsing...';
        feather.replace();

        fetch("{{ route('procurement.po.import-history') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderPreview(data);
            } else {
                alert(data.message || 'Failed to parse file');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Analysis failed. Please check file format.');
        })
        .finally(() => {
            btn.disabled = false;
            spinning.classList.add('hidden');
            text.textContent = 'Analyze File';
            feather.replace();
        });
    });

    function renderPreview(data) {
        const headerRow = document.getElementById('previewHeader');
        const body = document.getElementById('previewBody');
        
        headerRow.innerHTML = '';
        body.innerHTML = '';

        if (data.preview.length > 0) {
            const keys = Object.keys(data.preview[0]);
            const trHeader = document.createElement('tr');
            keys.forEach(key => {
                const th = document.createElement('th');
                th.className = "px-4 py-2 text-left text-[10px] font-bold text-gray-500 uppercase tracking-wider";
                th.textContent = key.replace(/_/g, ' ');
                trHeader.appendChild(th);
            });
            headerRow.appendChild(trHeader);

            data.preview.forEach(row => {
                const tr = document.createElement('tr');
                tr.className = "hover:bg-gray-50 dark:hover:bg-gray-700/50";
                keys.forEach(key => {
                    const td = document.createElement('td');
                    td.className = "px-4 py-2 whitespace-nowrap text-gray-700 dark:text-gray-300";
                    td.textContent = row[key];
                    tr.appendChild(td);
                });
                body.appendChild(tr);
            });
        }

        document.getElementById('totalRowsCount').textContent = data.total_rows;
        document.getElementById('tempPathInput').value = data.temp_path;
        document.getElementById('importRoleInput').value = data.import_role;
        
        document.getElementById('importStepUpload').classList.add('hidden');
        document.getElementById('importStepPreview').classList.remove('hidden');
    }

    document.addEventListener('DOMContentLoaded', function() {
        feather.replace();
    });
</script>
