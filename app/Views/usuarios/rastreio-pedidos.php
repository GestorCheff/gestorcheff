<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <h1 class="mb-0"><i class="bi bi-truck me-3 text-gradient-primary"></i>Meus Pedidos</h1>
        <a href="<?= base_url('cardapiousuario/cardapio/cardapio') ?>" class="btn btn-primary rounded-pill px-4">
            <i class="bi bi-plus-circle me-2"></i>Fazer Novo Pedido
        </a>
    </div>
    
    <?php if (empty($pedidos)): ?>
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body text-center py-5">
                <i class="bi bi-cart-x-fill display-4 text-muted mb-4"></i>
                <h3 class="fw-light mb-3">Nenhum pedido encontrado</h3>
                <p class="text-muted mb-4">Você ainda não fez nenhum pedido em nosso sistema.</p>
                <a href="<?= base_url('restaurantes') ?>" class="btn btn-primary rounded-pill px-4">
                    <i class="bi bi-shop me-2"></i>Explorar Restaurantes
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Nº Pedido</th>
                            <th>Restaurante</th>
                            <th>Data</th>
                            <th>Valor Total</th>
                            <th>Status</th>
                            <th class="pe-4 text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pedidos as $pedido): ?>
                            <tr class="align-middle">
                                <td class="ps-4 fw-bold"><?= $pedido['id'] ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <!-- img src=" base_url('uploads/restaurantes/' . ($pedido['restaurante_imagem'] ?? 'default.jpg')) ?>" 
                                             class="rounded-circle me-3" width="40" height="40" alt=" esc($pedido['restaurante_nome'] ?? 'Restaurante') ?>"> -->
                                        <span><?= esc($pedido['restaurante_nome'] ?? 'Restaurante') ?></span>
                                    </div>
                                </td>
                                <td><?= date('d/m/Y \à\s H:i', strtotime($pedido['criado_em'])) ?></td>
                                <td class="fw-bold">R$ <?= number_format($pedido['valor_total'], 2, ',', '.') ?></td>
                                <td>
                                    <span class="badge rounded-pill py-2 px-3 <?= getStatusBadgeClass($pedido['status']) ?>">
                                        <i class="bi <?= getStatusIcon($pedido['status']) ?> me-1"></i>
                                        <?= ucfirst($pedido['status']) ?>
                                    </span>
                                </td>
                                <td class="pe-4 text-end">
                                    <div class="d-flex justify-content-end">
                                        <a href="<?= base_url('pedidos/detalhes/' . $pedido['id']) ?>" 
                                        class="btn btn-sm btn-outline-primary rounded-pill me-2 d-flex align-items-center">
                                            <i class="bi bi-eye me-1"></i> Detalhes
                                        </a>
                                        
                                        <?php if ($pedido['status'] == 'enviado'): ?>
                                            <form method="post" action="<?= base_url('pedidos/confirmar-entrega/' . $pedido['id']) ?>" class="d-inline">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-outline-warning rounded-pill d-flex align-items-center cancelar-pedidor" 
                                                        onclick="return confirm('Confirmar recebimento do pedido #<?= $pedido['id'] ?>?')">
                                                    <i class="bi bi-patch-check me-1"></i> Recebido
                                                </button>
                                            </form>
                                        <?php elseif ($pedido['status'] == 'aguardando' || $pedido['status'] == 'preparando'): ?>
                                            <button class="btn btn-sm btn-outline-danger rounded-pill d-flex align-items-center cancelar-pedido" 
                                                    data-pedido-id="<?= $pedido['id'] ?>">
                                                <i class="bi bi-x-circle me-1"></i> Cancelar
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Paginação (se aplicável) -->
        <?php if (isset($pager)): ?>
            <div class="d-flex justify-content-center mt-4">
                <?= $pager->links() ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

 <!-- Lista de Pedidos em Entrega -->
 <?php if ($pedido['status'] == 'enviado'): ?>
     <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
         <div class="card-header bg-light">
             <h5 class="mb-0"><i class="bi bi-truck me-2"></i>Pedidos em Entrega</h5>
         </div>
         <div class="table-responsive">
             <table class="table table-hover mb-0">
                 <thead class="bg-light">
                     <tr>
                         <th class="ps-4">Nº Pedido</th>
                         <th>Restaurante</th>
                         <th>Data Envio</th>
                         <th>Valor Total</th>
                         <th>Status</th>
                         <th class="pe-4 text-end">Ações</th>
                     </tr>
                 </thead>
                 <tbody>
                     <?php foreach ($pedidos_entrega as $pedido): ?>
                         <tr class="align-middle">
                             <td class="ps-4 fw-bold"><?= $pedido['id'] ?></td>
                             <td>
                                 <div class="d-flex align-items-center">
                                     <span><?= esc($pedido['restaurante_nome'] ?? 'Restaurante') ?></span>
                                 </div>
                             </td>
                             <td><?= date('d/m/Y \à\s H:i', strtotime($pedido['envio_entrega'])) ?></td>
                             <td class="fw-bold">R$ <?= number_format($pedido['valor_total'], 2, ',', '.') ?></td>
                             <td>
                                 <span class="badge rounded-pill py-2 px-3 bg-warning text-dark">
                                     <i class="bi bi-truck me-1"></i>
                                     Em Entrega
                                 </span>
                             </td>
                             <td class="pe-4 text-end">
                                 <div class="d-flex justify-content-end">
                                     <a href="<?= base_url('pedidos/detalhes/' . $pedido['id']) ?>" 
                                        class="btn btn-sm btn-outline-primary rounded-pill me-2 d-flex align-items-center">
                                         <i class="bi bi-eye me-1"></i> Detalhes
                                     </a>
                                     <form method="post" action="<?= base_url('pedidos/confirmar-entrega/' . $pedido['id']) ?>" class="d-inline">
                                         <?= csrf_field() ?>
                                         <button type="submit" class="btn btn-sm btn-success rounded-pill d-flex align-items-center" 
                                                 onclick="return confirm('Confirmar recebimento do pedido #<?= $pedido['id'] ?>?')">
                                             <i class="bi bi-check-circle me-1"></i> Recebido
                                         </button>
                                     </form>
                                 </div>
                             </td>
                         </tr>
                     <?php endforeach; ?>
                 </tbody>
             </table>
         </div>
     </div>
 <?php endif; ?>

<!-- Modal de Confirmação de Cancelamento -->
<div class="modal fade" id="cancelarPedidoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-gradient-danger text-white border-0">
                <h5 class="modal-title"><i class="bi bi-exclamation-triangle-fill me-2"></i>Confirmar Cancelamento</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4">
                <div class="d-flex align-items-center mb-3">
                    <i class="bi bi-x-circle-fill text-danger me-3 fs-4"></i>
                    <div>
                        <h5 class="mb-1">Tem certeza que deseja cancelar este pedido?</h5>
                        <p class="text-muted mb-0">Esta ação não pode ser desfeita.</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Voltar</button>
                <button type="button" class="btn btn-danger rounded-pill px-4" id="confirmarCancelamento">
                    <i class="bi bi-trash me-1"></i> Confirmar
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link href="<?= base_url('css/rastreio-pedidos.css') ?>" type="text/css" rel="stylesheet" />

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    let pedidoIdParaCancelar;
    
    // Abre modal de cancelamento
    $('.cancelar-pedido').click(function() {
        pedidoIdParaCancelar = $(this).data('pedido-id');
        $('#cancelarPedidoModal').modal('show');
    });
    
    // Confirma cancelamento
    $('#confirmarCancelamento').click(function() {
        $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Processando...');
        
        $.ajax({
            url: '<?= base_url('pedidos/cancelar') ?>',
            method: 'POST',
            data: {
                pedido_id: pedidoIdParaCancelar,
                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    $('#cancelarPedidoModal').modal('hide');
                    alert(response.message || 'Erro ao cancelar pedido');
                }
            },
            error: function() {
                $('#cancelarPedidoModal').modal('hide');
                alert('Erro na comunicação com o servidor');
            },
            complete: function() {
                $('#confirmarCancelamento').prop('disabled', false).html('<i class="bi bi-trash me-1"></i> Confirmar');
            }
        });
    });
});
</script>
<?= $this->endSection() ?>

<?php
// Função auxiliar para retornar classes CSS baseadas no status (adicionar no controller ou helper)

// Função auxiliar para retornar ícones baseados no status
function getStatusIcon($status) {
    switch ($status) {
        case 'aguardando': return 'bi-clock';
        case 'preparando': return 'bi-egg-fried';
        case 'enviado': return 'bi bi-truck';
        case 'entregue': return 'bi-check-circle';
        case 'cancelado': return 'bi-x-circle';
        default: return 'bi-question-circle';
    }
}
?>