<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Pedidos - GestorCheff</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .painel-title {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(90deg, #4e54c8, #8f94fb);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: fadeIn 1s ease-in-out;
        }

        .card-hover {
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }

        .card-hover:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
            border-left-color: #4e54c8;
        }

        .badge {
            font-size: 0.85rem;
            padding: 0.5em 0.75em;
        }

        .address-container {
            white-space: normal;
            word-break: break-word;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Ajuste para telas menores */
        @media (max-width: 768px) {
            .order-card .col-md-2, 
            .order-card .col-md-3 {
                margin-bottom: 1rem;
            }
            
            .order-actions {
                justify-content: start !important;
                margin-top: 1rem;
            }
        }
    </style>
</head>
<body class="bg-light">

<div class="container py-5">
    <h1 class="painel-title text-center mb-5 fw-bold">📋 Pedidos Recebidos</h1>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success text-center">
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <!-- Filtros e Busca Funcionais -->
    <form method="get" action="<?= base_url('pedidos') ?>">
        <div class="row mb-4">
            <div class="col-md-6 mb-3 mb-md-0">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Buscar por nome, telefone ou ID..." value="<?= isset($_GET['search']) ? esc($_GET['search']) : '' ?>">
                </div>
            </div>
            <div class="col-md-4">
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">Todos os status</option>
                    <option value="aguardando" <?= (isset($_GET['status']) && $_GET['status'] == 'aguardando') ? 'selected' : '' ?>>Aguardando</option>
                    <option value="preparando" <?= (isset($_GET['status']) && $_GET['status'] == 'preparando') ? 'selected' : '' ?>>Preparando</option>
                    <option value="enviado" <?= (isset($_GET['status']) && $_GET['status'] == 'enviado') ? 'selected' : '' ?>>Enviado</option>
                    <option value="finalizado" <?= (isset($_GET['status']) && $_GET['status'] == 'finalizado') ? 'selected' : '' ?>>Finalizado</option>
                    <option value="cancelado" <?= (isset($_GET['status']) && $_GET['status'] == 'cancelado') ? 'selected' : '' ?>>Cancelado</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filtrar</button>
            </div>
        </div>
    </form>

    <!-- Lista de Pedidos -->
    <div class="row g-4">
        <?php if (!empty($pedidos)): ?>
            <?php foreach ($pedidos as $pedido): ?>
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 h-100 card-hover order-card">
                        <div class="card-body p-4">
                            <div class="row align-items-center">
                                <div class="col-md-2">
                                    <h6 class="text-muted mb-1">Pedido #<?= esc($pedido['id']) ?></h6>
                                    <small class="text-muted"><?= date('d/m/Y H:i', strtotime($pedido['criado_em'])) ?></small>
                                </div>
                                <div class="col-md-2">
                                    <h6 class="fw-bold mb-1"><?= esc($pedido['cliente_nome']) ?></h6>
                                    <small class="text-muted"><?= esc($pedido['cliente_telefone']) ?></small>
                                </div>
                                <div class="col-md-4 address-container">
                                    <p class="mb-1"><?= nl2br(esc($pedido['cliente_endereco'])) ?></p>
                                </div>
                                <div class="col-md-2 text-center">
                                    <h5 class="text-danger fw-bold">R$ <?= number_format($pedido['valor_total'], 2, ',', '.') ?></h5>
                                </div>
                                <div class="col-md-2 text-end">
                                    <span class="badge rounded-pill 
                                        <?= $pedido['status'] == 'aguardando' ? 'bg-warning text-dark' : '' ?>
                                        <?= $pedido['status'] == 'preparando' ? 'bg-primary' : '' ?>
                                        <?= $pedido['status'] == 'enviado' ? 'bg-info' : '' ?>
                                        <?= $pedido['status'] == 'finalizado' ? 'bg-success' : '' ?>
                                        <?= $pedido['status'] == 'cancelado' ? 'bg-danger' : '' ?>">
                                        <?= ucfirst($pedido['status']) ?>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="row mt-3">
                                <div class="col-12 d-flex justify-content-end gap-2 order-actions">
                                    <a href="<?= base_url('pedidos/detalhes/' . $pedido['id']) ?>" class="btn btn-sm btn-outline-secondary rounded-3 px-3">
                                        <i class="fas fa-eye me-1"></i> Detalhes
                                    </a>
                                    <?php if ($pedido['status'] == 'aguardando'): ?>
                                        <form method="post" action="<?= base_url('pedidos/confirmar/' . $pedido['id']) ?>" class="d-inline">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-success rounded-3 px-3">
                                                <i class="fas fa-check me-1"></i> Confirmar
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    
                                    <!-- Novo bloco para marcar como enviado -->
                                    <?php if ($pedido['status'] == 'preparando'): ?>
                                        <form method="post" action="<?= base_url('pedidos/enviar/' . $pedido['id']) ?>" class="d-inline">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-info rounded-3 px-3">
                                                <i class="fas fa-truck me-1"></i> Enviar
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                                                        
                                    <?php if ($pedido['status'] != 'cancelado' && $pedido['status'] != 'finalizado'): ?>
                                        <form method="post" action="<?= base_url('pedidos/cancelar/' . $pedido['id']) ?>" class="d-inline">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-3 px-3" onclick="return confirm('Tem certeza que deseja cancelar este pedido?');">
                                                <i class="fas fa-times me-1"></i> Cancelar
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center mt-5">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body py-5">
                        <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">Nenhum pedido encontrado</h4>
                        <p class="text-muted"><?= isset($_GET['search']) || isset($_GET['status']) ? 'Tente ajustar os filtros de busca.' : 'Quando novos pedidos chegarem, eles aparecerão aqui.' ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>