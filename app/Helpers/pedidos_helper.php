<?php

if (!function_exists('getStatusBadgeClass')) {
    function getStatusBadgeClass($status) {
        $classes = [
            'aguardando' => 'bg-warning text-dark',
            'preparando' => 'bg-info text-white',
            'enviado'    => 'bg-primary text-white',
            'finalizado'    => 'bg-success text-white',
            'entregue' => 'bg-success text-white',
            'cancelado'  => 'bg-secondary text-white'
        ];
        
        return $classes[strtolower($status)] ?? 'bg-light text-dark';
    }
}