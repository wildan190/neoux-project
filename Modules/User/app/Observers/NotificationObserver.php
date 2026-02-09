<?php

namespace Modules\User\Observers;

use App\Notifications\ModelOperationNotification;
use App\Modules\User\Domain\Models\User;
use App\Modules\Procurement\Domain\Models\GoodsReturnRequest;
use App\Modules\Procurement\Domain\Models\PurchaseOrder;
use App\Modules\Company\Domain\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Notification;

class NotificationObserver
{
    /**
     * Handle the Model "created" event.
     */
    public function created(Model $model): void
    {
        $this->notifyUsers($model, 'created');
    }

    /**
     * Handle the Model "updated" event.
     */
    public function updated(Model $model): void
    {
        $this->notifyUsers($model, 'updated');
    }

    /**
     * Handle the Model "deleted" event.
     */
    public function deleted(Model $model): void
    {
        $this->notifyUsers($model, 'deleted');
    }

    protected function notifyUsers(Model $model, string $action): void
    {
        $usersToNotify = collect();
        $customMessage = null;
        $currentUser = auth()->user();

        // Dispatch to specific handlers
        if ($model instanceof GoodsReturnRequest) {
            $this->handleGoodsReturnRequest($model, $action, $usersToNotify, $customMessage, $currentUser);
        } elseif ($model instanceof \App\Modules\Procurement\Domain\Models\Invoice) {
            $this->handleInvoice($model, $action, $usersToNotify, $customMessage, $currentUser);
        } elseif ($model instanceof \App\Modules\Procurement\Domain\Models\DebitNote) {
            // Add generic handler for DebitNote for now, or specific if needed
            $this->handleGenericModel($model, $action, $usersToNotify, $currentUser);
        } else {
            $this->handleGenericModel($model, $action, $usersToNotify, $currentUser);
        }

        // Always notify the current user for feedback (optional, but good for confirmation)
        if ($currentUser) {
            $usersToNotify->push($currentUser);
        }

        // Remove duplicates
        $usersToNotify = $usersToNotify->unique('id');

        if ($usersToNotify->isEmpty()) {
            return;
        }

        Notification::send($usersToNotify, new ModelOperationNotification($model, $action, $customMessage));
    }

    protected function handleGenericModel($model, $action, &$usersToNotify, $currentUser)
    {
        // Default logic: Notify owner
        if ($model->user_id) {
            $owner = User::find($model->user_id);
            if ($owner && (!$currentUser || $owner->id !== $currentUser->id)) {
                $usersToNotify->push($owner);
            }
        }
    }

    protected function handleGoodsReturnRequest(GoodsReturnRequest $grr, string $action, &$usersToNotify, &$customMessage, $currentUser)
    {
        $grr->loadMissing([
            'goodsReceiptItem.goodsReceipt.purchaseOrder.purchaseRequisition.company',
            'goodsReceiptItem.goodsReceipt.purchaseOrder.vendorCompany'
        ]);

        $po = $grr->goodsReceiptItem?->goodsReceipt?->purchaseOrder;

        if (!$po)
            return;

        $buyerCompany = $po->purchaseRequisition?->company;
        $vendorCompany = $po->vendorCompany;

        if (!$buyerCompany || !$vendorCompany)
            return;

        $isBuyerAction = $this->isUserInCompany($currentUser, $buyerCompany);
        $isVendorAction = $this->isUserInCompany($currentUser, $vendorCompany);

        $grrNumber = $grr->grr_number;

        if ($action === 'created') {
            if ($isBuyerAction) {
                $this->addCompanyUsers($vendorCompany, $usersToNotify);
                $customMessage = "Buyer has issued a new Goods Return Request ($grrNumber). Please review.";
            }
        } elseif ($action === 'updated') {
            if ($grr->wasChanged('resolution_type') && $grr->resolution_status === 'pending') {
                if ($isBuyerAction) {
                    $this->addCompanyUsers($vendorCompany, $usersToNotify);
                    $customMessage = "Buyer has requested resolution '{$grr->resolution_type_label}' for $grrNumber.";
                }
            } elseif ($grr->wasChanged('resolution_status')) {
                if ($grr->resolution_status === 'approved_by_vendor') {
                    $this->addCompanyUsers($buyerCompany, $usersToNotify);
                    $customMessage = "Vendor has APPROVED your resolution request for $grrNumber.";
                } elseif ($grr->resolution_status === 'rejected_by_vendor') {
                    $this->addCompanyUsers($buyerCompany, $usersToNotify);
                    $customMessage = "Vendor has REJECTED your resolution request for $grrNumber.";
                }
            } else {
                $customMessage = "Update on GRR $grrNumber.";
                if ($isBuyerAction)
                    $this->addCompanyUsers($vendorCompany, $usersToNotify);
                elseif ($isVendorAction)
                    $this->addCompanyUsers($buyerCompany, $usersToNotify);
            }
        }
    }

    protected function handleInvoice(\App\Modules\Procurement\Domain\Models\Invoice $invoice, string $action, &$usersToNotify, &$customMessage, $currentUser)
    {
        $invoice->loadMissing(['purchaseOrder.vendorCompany', 'purchaseOrder.purchaseRequisition.company']);

        $po = $invoice->purchaseOrder;
        if (!$po)
            return;

        $vendorCompany = $po->vendorCompany; // Issuer
        $buyerCompany = $po->purchaseRequisition?->company; // Payer

        if (!$vendorCompany || !$buyerCompany)
            return;

        $isVendorAction = $this->isUserInCompany($currentUser, $vendorCompany);
        $isBuyerAction = $this->isUserInCompany($currentUser, $buyerCompany);

        $invNumber = $invoice->invoice_number;

        if ($action === 'created') {
            if ($isVendorAction) {
                $this->addCompanyUsers($buyerCompany, $usersToNotify);
                $customMessage = "New Invoice $invNumber issued by {$vendorCompany->name}.";
            } elseif ($isBuyerAction) {
                $this->addCompanyUsers($vendorCompany, $usersToNotify);
                $customMessage = "Invoice $invNumber created by Buyer.";
            }
        } elseif ($action === 'updated') {
            if ($invoice->wasChanged('status')) {
                if ($invoice->status === 'paid') {
                    $this->addCompanyUsers($vendorCompany, $usersToNotify);
                    $customMessage = "Invoice $invNumber has been PAID.";
                } elseif ($invoice->status === 'approved') {
                    $this->addCompanyUsers($vendorCompany, $usersToNotify);
                    $customMessage = "Invoice $invNumber has been APPROVED for payment.";
                } elseif ($invoice->status === 'rejected') {
                    $this->addCompanyUsers($vendorCompany, $usersToNotify);
                    $customMessage = "Invoice $invNumber was REJECTED.";
                }
            } else {
                $customMessage = "Invoice $invNumber updated.";
                if ($isVendorAction)
                    $this->addCompanyUsers($buyerCompany, $usersToNotify);
                elseif ($isBuyerAction)
                    $this->addCompanyUsers($vendorCompany, $usersToNotify);
            }
        }
    }

    protected function isUserInCompany(?User $user, Company $company): bool
    {
        if (!$user)
            return false;
        if ($company->user_id === $user->id)
            return true;
        return $company->members()->where('users.id', $user->id)->exists();
    }

    protected function addCompanyUsers(Company $company, &$usersCollection)
    {
        if ($company->user) {
            $usersCollection->push($company->user);
        }
        foreach ($company->members as $member) {
            $usersCollection->push($member);
        }
    }
}
