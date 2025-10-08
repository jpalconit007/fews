<?php
/**
 * Status Legend Component
 * 
 * Displays a color-coded status legend with labels
 */
function statusLegend() {
    $statuses = [
        'Normal' => '#4CAF50',  // Green
        //'Low' => '#FFFF00',     // Blue
        'Moderate' => '#ffff00',  // Yellow
        'Warning' => '#ff8c00',     //Orange
        'Critical' => '#F44336'     //Red
    ];
    
    echo '<div class="status-legend">';
    echo '<p><strong>Status &nbsp| Legend</strong></p>';
    echo '<div class="legend-items">';
    
    foreach ($statuses as $status => $color) {
        echo '<div class="legend-item">';
        echo '<span class="status-label">' . $status . '</span>';
        echo '<span class="color-box" style="background-color: ' . $color . '"></span>';
        echo '</div>';
    }
    
    echo '</div>';
    echo '</div>';
}
?>
