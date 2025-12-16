<?php
/**
 * Helper: booking_format
 *
 * ใช้สำหรับ "แสดงผล" ช่วงวันเวลาให้เป็นมิตรกับผู้ใช้ (endDate inclusive)
 * โดยยังคงแนวคิดเวลาแบบ [start, end) (end exclusive) ในชั้น logic/DB ได้เหมือนเดิม
 */

if (! function_exists('booking_format_range')) {
    /**
     * @param string|null $startStr  รูปแบบ Y-m-d H:i:s
     * @param string|null $endStr    รูปแบบ Y-m-d H:i:s
     * @return array{
     *   type: 'hourly'|'daily'|'range'|'unknown',
     *   startDate: string,
     *   endDate: string,
     *   startTime: string,
     *   endTime: string,
     *   days: int,
     *   label: string
     * }
     */
    function booking_format_range(?string $startStr, ?string $endStr): array
    {
        $unknown = [
            'type' => 'unknown',
            'startDate' => '-',
            'endDate' => '-',
            'startTime' => '-',
            'endTime' => '-',
            'days' => 0,
            'label' => '-',
        ];

        if (! $startStr || ! $endStr) {
            return $unknown;
        }

        $startTs = strtotime($startStr);
        $endTs   = strtotime($endStr);

        if ($startTs === false || $endTs === false || $endTs <= $startTs) {
            return $unknown;
        }

        $startDate = date('d/m/Y', $startTs);
        $endDate   = date('d/m/Y', $endTs);
        $startTime = date('H:i', $startTs);
        $endTime   = date('H:i', $endTs);

        $duration = $endTs - $startTs; // seconds

        // Daily (exclusive end): start at 00:00, end at 00:00, duration multiple of 1 day and >= 1 day
        $isStartMidnight = (date('H:i:s', $startTs) === '00:00:00');
        $isEndMidnight   = (date('H:i:s', $endTs) === '00:00:00');
        $isEndLastSecond = (date('H:i:s', $endTs) === '23:59:59');

        if ($isStartMidnight && $isEndMidnight && $duration >= 86400 && ($duration % 86400) === 0) {
            // inclusive display end = end - 1 second
            $displayEndTs = $endTs - 1;
            $displayEndDate = date('d/m/Y', $displayEndTs);
            $days = (int) ($duration / 86400);

            $label = $days <= 1
                ? ($startDate . ' (1 วัน)')
                : ($startDate . ' ถึง ' . $displayEndDate . ' (' . $days . ' วัน)');

            return [
                'type' => 'daily',
                'startDate' => $startDate,
                'endDate' => $displayEndDate,
                'startTime' => '00:00',
                'endTime' => '23:59',
                'days' => $days,
                'label' => $label,
            ];
        }

        // Daily (inclusive stored): start 00:00 and end 23:59:59, duration is N days - 1 second
        if ($isStartMidnight && $isEndLastSecond && $duration >= 86399 && (($duration + 1) % 86400) === 0) {
            $days = (int) (($duration + 1) / 86400);

            $label = $days <= 1
                ? ($startDate . ' (1 วัน)')
                : ($startDate . ' ถึง ' . $endDate . ' (' . $days . ' วัน)');

            return [
                'type' => 'daily',
                'startDate' => $startDate,
                'endDate' => $endDate,
                'startTime' => '00:00',
                'endTime' => '23:59',
                'days' => $days,
                'label' => $label,
            ];
        }

        // Hourly (same day)
        if ($startDate === $endDate) {
            return [
                'type' => 'hourly',
                'startDate' => $startDate,
                'endDate' => $endDate,
                'startTime' => $startTime,
                'endTime' => $endTime,
                'days' => 0,
                'label' => $startDate . ' ' . $startTime . '-' . $endTime . ' น.',
            ];
        }

        // Fallback: range across days with time
        return [
            'type' => 'range',
            'startDate' => $startDate,
            'endDate' => $endDate,
            'startTime' => $startTime,
            'endTime' => $endTime,
            'days' => 0,
            'label' => $startDate . ' ' . $startTime . ' ถึง ' . $endDate . ' ' . $endTime . ' น.',
        ];
    }
}
