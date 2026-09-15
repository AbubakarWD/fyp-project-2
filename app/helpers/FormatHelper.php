<?php
/**
 * BloodLife — Format Helper
 * Provides UI formatting routines, date helpers, urgency badges, and ABO compatibility logic.
 */

class FormatHelper {

    /**
     * ABO Blood Compatibility Chart mapping.
     */
    private static array $compatibilityMatrix = [
        'A+'  => ['donate' => ['A+', 'AB+'], 'receive' => ['A+', 'A-', 'O+', 'O-']],
        'A-'  => ['donate' => ['A+', 'A-', 'AB+', 'AB-'], 'receive' => ['A-', 'O-']],
        'B+'  => ['donate' => ['B+', 'AB+'], 'receive' => ['B+', 'B-', 'O+', 'O-']],
        'B-'  => ['donate' => ['B+', 'B-', 'AB+', 'AB-'], 'receive' => ['B-', 'O-']],
        'AB+' => ['donate' => ['AB+'], 'receive' => ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']],
        'AB-' => ['donate' => ['AB+', 'AB-'], 'receive' => ['A-', 'B-', 'AB-', 'O-']],
        'O+'  => ['donate' => ['O+', 'A+', 'B+', 'AB+'], 'receive' => ['O+', 'O-']],
        'O-'  => ['donate' => ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'], 'receive' => ['O-']],
    ];

    /**
     * Check if a donor blood group can donate to recipient blood group.
     *
     * @param string $donorGroup
     * @param string $recipientGroup
     * @return bool
     */
    public static function isCompatible(string $donorGroup, string $recipientGroup): bool {
        $donorGroup = strtoupper(trim($donorGroup));
        $recipientGroup = strtoupper(trim($recipientGroup));

        if (isset(self::$compatibilityMatrix[$donorGroup])) {
            return in_array($recipientGroup, self::$compatibilityMatrix[$donorGroup]['donate']);
        }
        return false;
    }

    /**
     * Get compatible recipient blood groups for a given donor blood group.
     *
     * @param string $donorGroup
     * @return array
     */
    public static function getCanDonateTo(string $donorGroup): array {
        $donorGroup = strtoupper(trim($donorGroup));
        return self::$compatibilityMatrix[$donorGroup]['donate'] ?? [];
    }

    /**
     * Format a date into human-readable relative time (e.g., "2 hours ago", "Yesterday").
     *
     * @param string|int $datetime
     * @return string
     */
    public static function timeAgo($datetime): string {
        $timestamp = is_numeric($datetime) ? (int)$datetime : strtotime($datetime);
        if (!$timestamp) {
            return 'N/A';
        }

        $diff = time() - $timestamp;
        if ($diff < 60) {
            return 'Just now';
        }
        
        $intervals = [
            31536000 => 'year',
            2592000  => 'month',
            604800   => 'week',
            86400    => 'day',
            3600     => 'hour',
            60       => 'minute'
        ];

        foreach ($intervals as $secs => $label) {
            $count = floor($diff / $secs);
            if ($count >= 1) {
                return $count . ' ' . $label . ($count > 1 ? 's' : '') . ' ago';
            }
        }

        return 'Just now';
    }

    /**
     * Format date nicely (e.g. Sep 14, 2026).
     *
     * @param string|null $date
     * @param string $format
     * @return string
     */
    public static function formatDate(?string $date, string $format = 'M d, Y'): string {
        if (!$date || $date === '0000-00-00') {
            return 'N/A';
        }
        $ts = strtotime($date);
        return $ts ? date($format, $ts) : 'N/A';
    }

    /**
     * Render an urgency level HTML badge element.
     *
     * @param string $urgency 'low', 'medium', 'high', 'critical'
     * @return string
     */
    public static function urgencyBadge(string $urgency): string {
        $urgency = strtolower(trim($urgency));
        $classMap = [
            'critical' => 'bl-badge-urgency-critical',
            'high'     => 'bl-badge-urgency-high',
            'medium'   => 'bl-badge-urgency-medium',
            'low'      => 'bl-badge-urgency-low',
        ];
        $class = $classMap[$urgency] ?? 'bl-badge-urgency-medium';
        $label = ucfirst($urgency);

        return '<span class="bl-badge ' . $class . '"><i class="bi bi-exclamation-circle-fill me-1"></i>' . SecurityHelper::e($label) . '</span>';
    }

    /**
     * Render blood request status HTML badge.
     *
     * @param string $status 'active', 'partially_fulfilled', 'fulfilled', 'cancelled', 'expired'
     * @return string
     */
    public static function statusBadge(string $status): string {
        $status = strtolower(trim($status));
        $map = [
            'active'              => ['class' => 'bl-badge-primary', 'label' => 'Active'],
            'partially_fulfilled' => ['class' => 'bl-badge-warning', 'label' => 'Partial'],
            'fulfilled'           => ['class' => 'bl-badge-success', 'label' => 'Fulfilled'],
            'cancelled'           => ['class' => 'bl-badge-secondary', 'label' => 'Cancelled'],
            'expired'             => ['class' => 'bl-badge-danger', 'label' => 'Expired'],
        ];

        $info = $map[$status] ?? ['class' => 'bl-badge-secondary', 'label' => ucfirst($status)];
        return '<span class="bl-badge ' . $info['class'] . '">' . SecurityHelper::e($info['label']) . '</span>';
    }
}
