<?php
/**
 * BloodLife — Flash Toast Notification Helper View
 * Automatically renders session flash messages using design-system alert tokens.
 */
require_once __DIR__ . '/../../app/helpers/SessionHelper.php';
require_once __DIR__ . '/../../app/helpers/SecurityHelper.php';

$flash = SessionHelper::getFlash();
if ($flash):
    $type = $flash['type'] ?? 'info';
    $message = $flash['message'] ?? '';
    
    $alertClass = 'bl-alert-info';
    $iconClass = 'bi-info-circle-fill';
    
    if ($type === 'success') {
        $alertClass = 'bl-alert-success';
        $iconClass = 'bi-check-circle-fill';
    } elseif ($type === 'danger' || $type === 'error') {
        $alertClass = 'bl-alert-danger';
        $iconClass = 'bi-x-circle-fill';
    } elseif ($type === 'warning') {
        $alertClass = 'bl-alert-warning';
        $iconClass = 'bi-exclamation-triangle-fill';
    }
?>
<div class="container mt-3">
    <div class="bl-alert <?= $alertClass ?> bl-alert-auto-dismiss align-items-center" role="alert">
        <i class="bi <?= $iconClass ?> fs-5"></i>
        <div class="flex-grow-1 font-semibold"><?= e($message) ?></div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
</div>
<?php endif; ?>
