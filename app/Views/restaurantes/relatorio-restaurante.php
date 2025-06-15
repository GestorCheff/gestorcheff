<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatórios de Restaurantes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
    <!-- Estilo Personalizado -->
    <link href="<?= base_url('css/relatorio-restaurante.css') ?>" type="text/css" rel="stylesheet" />
</head>
<body>
<div class="container mt-4">
    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-message alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title"><i class="fas fa-chart-line me-2"></i>Relatórios de Restaurantes</h1>
            <p class="text-muted mb-0">Análise completa do desempenho dos restaurantes</p>
        </div>
        <div>
            <a href="<?= base_url('relatorios/sincronizar') ?>" class="btn btn-sync me-2" id="sync-btn">
                <i class="fas fa-sync-alt me-1"></i> Sincronizar Dados
            </a>
            <button class="btn btn-sm btn-outline-secondary" id="refresh-btn">
                <i class="fas fa-redo me-1"></i> Atualizar
            </button>
        </div>
    </div>

    <!-- Cards de Estatísticas Gerais -->
    <div class="row">
        <div class="col-md-3">
            <div class="card card-stat">
                <div class="card-body text-center">
                    <div class="stat-icon"><i class="fas fa-store"></i></div>
                    <div class="stat-value"><?= count($relatorios) ?></div>
                    <div class="stat-label">Restaurantes</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-stat">
                <div class="card-body text-center">
                    <div class="stat-icon"><i class="fas fa-shopping-bag"></i></div>
                    <div class="stat-value"><?= $estatisticas['total_pedidos'] ?? 0 ?></div>
                    <div class="stat-label">Pedidos Totais</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-stat">
                <div class="card-body text-center">
                    <div class="stat-icon"><i class="fas fa-money-bill-wave"></i></div>
                    <div class="stat-value">R$ <?= number_format($estatisticas['receita_total'] ?? 0, 2, ',', '.') ?></div>
                    <div class="stat-label">Receita Total</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-stat">
                <div class="card-body text-center">
                    <div class="stat-icon"><i class="fas fa-star"></i></div>
                    <div class="stat-value"><?= number_format($estatisticas['avaliacao_media'] ?? 0, 1) ?></div>
                    <div class="stat-label">Avaliação Média</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabela de Relatórios -->
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-body p-0">
            <table id="tabela-relatorios" class="table table-hover" style="width:100%">
                <thead>
                    <tr>
                        <th>Restaurante</th>
                        <th>Pedidos (Total/30d)</th>
                        <th>Receita (Total/30d)</th>
                        <th>Avaliação</th>
                        <th>Clientes</th>
                        <th>Top Produto</th>
                        <th>Atualizado em</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($relatorios as $relatorio): ?>
                        <tr>
                            <td>
                                <strong><?= esc($relatorio['nome_restaurante']) ?></strong>
                            </td>
                            <td>
                                <span class="badge bg-primary"><?= $relatorio['total_pedidos'] ?></span>
                                <span class="badge bg-info"><?= $relatorio['pedidos_30dias'] ?></span>
                            </td>
                            <td>
                                <span class="d-block">R$ <?= number_format($relatorio['receita_total'], 2, ',', '.') ?></span>
                                <small class="text-muted">R$ <?= number_format($relatorio['receita_30dias'], 2, ',', '.') ?> (30d)</small>
                            </td>
                            <td>
                                <span class="badge badge-rating">
                                    <i class="fas fa-star me-1"></i> <?= number_format($relatorio['avaliacao_media'], 1) ?>
                                </span>
                            </td>
                            <td><?= $relatorio['total_clientes'] ?></td>
                            <td>
                                <span class="badge bg-light text-dark"><?= esc($relatorio['produto_mais_vendido']) ?></span>
                            </td>
                            <td><?= date('d/m/Y H:i', strtotime($relatorio['atualizado_em'])) ?></td>
                            <td>
                                <a href="<?= base_url('relatorios/relatorio/' . $relatorio['restaurante_id']) ?>" 
                                   class="btn btn-sm btn-outline-primary" 
                                   title="Ver detalhes">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- JS Libraries -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json"></script>

<script>
    $(document).ready(function() {
        $('#tabela-relatorios').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
            },
            dom: "<'row'<'col-sm-12 col-md-6'B><'col-sm-12 col-md-6'f>>" +
                 "<'row'<'col-sm-12'tr>>" +
                 "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            buttons: [
                {
                    extend: 'excel',
                    text: '<i class="fas fa-file-excel me-1"></i> Excel',
                    className: 'btn-export'
                },
                {
                    extend: 'pdf',
                    text: '<i class="fas fa-file-pdf me-1"></i> PDF',
                    className: 'btn-export'
                },
                {
                    extend: 'print',
                    text: '<i class="fas fa-print me-1"></i> Imprimir',
                    className: 'btn-export'
                }
            ],
            responsive: true,
            order: [[2, 'desc']] // Ordenar por receita total decrescente
        });
        
        $('#refresh-btn').click(function() {
            location.reload();
        });
        
        // Adicionar efeito de loading ao botão de sincronizar
        $('#sync-btn').click(function(e) {
            var btn = $(this);
            btn.html('<i class="fas fa-spinner fa-spin me-1"></i> Sincronizando...');
            btn.prop('disabled', true);
        });
        
        // Fechar alerta automaticamente após 5 segundos
        setTimeout(function() {
            $('.alert-message').fadeOut('slow');
        }, 5000);
    });
</script>
</body>
</html>