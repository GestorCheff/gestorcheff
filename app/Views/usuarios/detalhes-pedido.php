<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Estilo Personalizado -->
<link href="<?= base_url('css/detalhes-pedido.css') ?>" type="text/css" rel="stylesheet" />

<div class="container py-5">
    <div class="card card-detail">
        <div class="card-header text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0"><i class="fas fa-receipt me-2"></i>Pedido #<?= $pedido['id'] ?></h2>
                <span class="badge <?= getStatusBadgeClass($pedido['status']) ?>">
                    <?= ucfirst($pedido['status']) ?>
                </span>
            </div>
        </div>
        
        <div class="card-body">
            <div class="row">
                <div class="col-lg-8">
                    <div class="info-box">
                        <h5><i class="fas fa-info-circle me-2"></i>Informações do Pedido</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong><i class="far fa-calendar-alt me-2"></i>Data:</strong> <?= date('d/m/Y H:i', strtotime($pedido['criado_em'])) ?></p>
                                <p><strong><i class="fas fa-money-bill-wave me-2"></i>Valor Total:</strong> R$ <?= number_format($pedido['valor_total'], 2, ',', '.') ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong><i class="fas fa-truck me-2"></i>Tipo de Entrega:</strong> <?= $pedido['tipo_entrega'] ?? 'Delivery' ?></p>
                                <?php if(isset($pedido['forma_pagamento'])): ?>
                                <p><strong><i class="fas fa-credit-card me-2"></i>Pagamento:</strong> <?= ucfirst($pedido['forma_pagamento']) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="info-box">
                        <h5><i class="fas fa-map-marker-alt me-2"></i>Informações de Entrega</h5>
                        <p><strong>Cliente:</strong> <?= esc($pedido['cliente_nome']) ?></p>
                        <p><strong>Endereço:</strong> <?= esc($pedido['cliente_endereco']) ?></p>
                        <p><strong>Telefone:</strong> <?= esc($pedido['cliente_telefone']) ?></p>
                        <?php if(!empty($pedido['observacoes'])): ?>
                        <p class="mt-3"><strong><i class="fas fa-sticky-note me-2"></i>Observações:</strong></p>
                        <p class="text-muted"><?= esc($pedido['observacoes']) ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="info-box">
                        <h5><i class="fas fa-utensils me-2"></i>Itens do Pedido</h5>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th class="text-center">Qtd</th>
                                        <th class="text-end">Preço Unitário</th>
                                        <th class="text-end">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($itens)): ?>
                                        <?php foreach ($itens as $item): ?>
                                            <tr>
                                                <td><?= esc($item['item_nome']) ?></td>
                                                <td class="text-center"><?= $item['quantidade'] ?></td>
                                                <td class="text-end">R$ <?= number_format($item['preco_unitario'], 2, ',', '.') ?></td>
                                                <td class="text-end">R$ <?= number_format($item['preco_total'], 2, ',', '.') ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="4" class="text-center">Nenhum item encontrado.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold">Total</td>
                                        <td class="text-end fw-bold">R$ <?= number_format($pedido['valor_total'], 2, ',', '.') ?></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="info-box">
                        <h5><i class="fas fa-history me-2"></i>Status do Pedido</h5>
                        <div class="status-timeline">
                            <!-- Pedido Recebido - Sempre ativo (exceto se cancelado) -->
                            <div class="status-step <?= $pedido['status'] == 'cancelado' ? 'canceled' : 'completed' ?>">
                                <div class="step-circle"></div>
                                <div class="step-content">
                                    <p class="mb-1">Pedido Recebido</p>
                                    <small class="text-muted"><?= date('d/m H:i', strtotime($pedido['criado_em'])) ?></small>
                                </div>
                            </div>
                            
                            <!-- Em Preparação -->
                            <div class="status-step <?= 
                                $pedido['status'] == 'preparando' ? 'active' : 
                                (in_array($pedido['status'], ['enviado', 'entregue']) ? 'completed' : '') 
                            ?>">
                                <div class="step-circle"></div>
                                <div class="step-content">
                                    <p class="mb-1">Em Preparação</p>
                                    <?php if(isset($pedido['inicio_preparo'])): ?>
                                    <small class="text-muted"><?= date('d/m H:i', strtotime($pedido['inicio_preparo'])) ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Enviado para Entrega -->
                            <div class="status-step <?= 
                                $pedido['status'] == 'enviado' ? 'active' : 
                                ($pedido['status'] == 'entregue' ? 'completed' : '') 
                            ?>">
                                <div class="step-circle"></div>
                                <div class="step-content">
                                    <p class="mb-1">Enviado para Entrega</p>
                                    <?php if(isset($pedido['envio_entrega'])): ?>
                                    <small class="text-muted"><?= date('d/m H:i', strtotime($pedido['envio_entrega'])) ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Pedido Entregue -->
                            <div class="status-step <?= $pedido['status'] == 'entregue' ? 'completed' : '' ?>">
                                <div class="step-circle"></div>
                                <div class="step-content">
                                    <p class="mb-1">Pedido Entregue</p>
                                    <?php if(isset($pedido['data_entrega'])): ?>
                                    <small class="text-muted"><?= date('d/m H:i', strtotime($pedido['data_entrega'])) ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <?php if($pedido['status'] == 'cancelado'): ?>
                            <div class="status-step canceled">
                                <div class="step-circle"></div>
                                <div class="step-content">
                                    <p class="mb-1">Pedido Cancelado</p>
                                    <?php if(isset($pedido['data_cancelamento'])): ?>
                                    <small class="text-muted"><?= date('d/m H:i', strtotime($pedido['data_cancelamento'])) ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-between mt-4">
                <a href="<?= base_url('pedidos/rastrear') ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Voltar para Meus Pedidos
                </a>
                
                <?php if (in_array($pedido['status'], ['aguardando', 'preparando'])): ?>
                <button class="btn btn-danger cancelar-pedido" data-pedido-id="<?= $pedido['id'] ?>">
                    <i class="fas fa-times-circle me-2"></i> Cancelar Pedido
                </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Cancelamento -->
<div class="modal fade" id="cancelarPedidoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i>Confirmar Cancelamento</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja cancelar este pedido?</p>
                <p class="text-muted">Esta ação não pode ser desfeita e pode estar sujeita a políticas de cancelamento.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-arrow-left me-2"></i>Voltar</button>
                <button type="button" class="btn btn-danger" id="confirmarCancelamento">
                    <i class="fas fa-trash-alt me-2"></i>Confirmar Cancelamento
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
<script>
$(document).ready(function () {
    let pedidoId;

    $('.cancelar-pedido').click(function () {
        pedidoId = $(this).data('pedido-id');
        $('#cancelarPedidoModal').modal('show');
    });

    $('#confirmarCancelamento').click(function () {
        $.post('<?= base_url('pedidos/cancelar') ?>', {
            pedido_id: pedidoId,
            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
        }, function (res) {
            if (res.success) {
                location.reload();
            } else {
                alert(res.message || 'Erro ao cancelar pedido');
            }
        }).fail(function () {
            alert('Erro ao comunicar com o servidor.');
        });
    });
});
</script>
<?= $this->endSection() ?>