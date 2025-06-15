<?php

if (!function_exists('getStatusBadgeClass')) {
    function getStatusBadgeClass(string $status): string
    {
        return match ($status) {
            'aguardando' => 'bg-secondary',
            'preparando' => 'bg-warning text-dark',
            'enviado'    => 'bg-info text-white',
            'finalizado'    => 'bg-success text-white',
            'entregue' => 'bg-success',
            'cancelado'  => 'bg-danger',
            default      => 'bg-light text-dark',
        };
    }
}
