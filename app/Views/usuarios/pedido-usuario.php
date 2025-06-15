<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <title>Cardápio - GestorCheff</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    
    <!-- Ícones Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

    <!-- Estilo Personalizado -->
    <link href="<?= base_url('css/pedido-usuario.css') ?>" type="text/css" rel="stylesheet" />
    <style>
        /* Estilo adicional para os seletores de quantidade */
        .quantidade-selector {
            width: 70px;
            display: inline-block;
        }
        
        .quantidade-selector input {
            text-align: center;
            padding: 0.25rem;
        }
        
        /* Ajustes para os cards de itens */
        .card-item {
            transition: transform 0.2s;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        
        .card-item:hover {
            transform: translateY(-5px);
        }
        
        .card-body {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        
        .btn-add {
            margin-top: auto;
        }
        
        /* Estilo para a seção de endereços */
        .selecao-endereco {
            border-top: 1px solid #dee2e6;
            padding-top: 1.5rem;
            margin-top: 1.5rem;
        }
        
        .endereco-option {
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .endereco-option:hover {
            background-color: #f8f9fa;
            border-color: #0d6efd;
        }
        
        .endereco-option.selected {
            background-color: #e7f1ff;
            border-color: #0d6efd;
        }
    </style>
</head>
<body>

    <!-- Importa o Navmenu -->
    <?= view('/layouts/navmenu') ?>



    <!-- Cabeçalho do Cardápio -->
    <div class="header-cardapio">
        <div class="container">
            <img src="https://cdn-icons-png.flaticon.com/512/706/706164.png" alt="Ícone de cardápio" class="cardapio-image" />
            <h1 class="display-4 fw-bold mb-3">Cardápio Digital</h1>
            <p class="lead mb-0">Sabores que encantam, experiências que ficam</p>
        </div>
    </div>

    <!-- Conteúdo Principal -->
    <div class="container mb-5">
        <?php 
        // Agrupa os itens por categoria
        $categorias = [];
        foreach ($itens_cardapio as $item) {
            $categoria = $item['categoria'] ?? 'Outros';
            $categorias[$categoria][] = $item;
        }
        ?>


        <?php foreach ($categorias as $categoria => $itens): ?>
            <h3 class="categoria-title">
                <i class="bi bi-bookmark-fill me-2"></i><?= esc($categoria) ?>
            </h3>
            <div class="row g-4">
                <?php foreach ($itens as $item): ?>
                    <div class="col-md-4 col-lg-3">
                        <div class="card-item shadow-sm">
                            <?php
                            $imagemUrl = !empty($item['imagem'])
                                ? base_url('uploads/' . $item['imagem'])
                                : base_url('uploads/cardapio/default.png');
                            ?>
                            <img 
                                src="<?= $imagemUrl ?>"
                                class="card-img-top img-fluid"
                                alt="<?= esc($item['nome']) ?>" />
                            <div class="card-body">
                                <h5 class="card-title"><?= esc($item['nome']) ?></h5>
                                <p class="card-text text-muted"><?= esc($item['descricao']) ?></p>
                                <div class="price fw-bold mb-2">
                                    R$ <?= number_format($item['preco'], 2, ',', '.') ?>
                                </div>
                                <div class="d-flex align-items-center mt-auto">
                                    <div class="quantidade-selector me-2">
                                        <input 
                                            type="number" min="1" max="20" value="1" 
                                            class="form-control form-control-sm quantidade-input" 
                                            id="qtd-<?= $item['id'] ?>">
                                    </div>
                                    <button 
                                        class="btn btn-add btn-sm flex-grow-1"
                                        onclick="adicionarItem(event, <?= $item['id'] ?>, '<?= esc($item['nome']) ?>', <?= $item['preco'] ?>, '<?= esc($item['descricao']) ?>')">
                                        <i class="bi bi-cart-plus me-2"></i>Adicionar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Botão flutuante do carrinho -->
    <button 
        class="btn-carrinho" 
        data-bs-toggle="offcanvas" 
        data-bs-target="#carrinhoOffcanvas" 
        aria-label="Abrir carrinho">
        <i class="bi bi-cart3"></i>
        <span class="badge-carrinho" id="contadorItens">0</span>
    </button>

    <!-- Offcanvas do carrinho -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="carrinhoOffcanvas" aria-labelledby="carrinhoLabel">
        <div class="offcanvas-header bg-primary text-white">
            <h5 class="offcanvas-title" id="carrinhoLabel">
                <i class="bi bi-cart3 me-2"></i>Seu Pedido
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Fechar"></button>
        </div>
        <div class="offcanvas-body">
            <form id="formPedido" action="<?= base_url('pedidos/salvar') ?>" method="post">
                <input type="hidden" name="itens" id="inputItens" />
                <input type="hidden" name="endereco_id" id="enderecoSelecionado" />

                <!-- Carrinho -->
                <table class="table table-carrinho" id="tabelaPedido" style="display: none;">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Qtd</th>
                            <th>Subtotal</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>

                <div id="carrinhoVazio" class="empty-cart text-center py-5">
                    <i class="bi bi-cart-x fs-1 text-muted"></i>
                    <p class="text-muted mt-2">Seu carrinho está vazio</p>
                </div>

                <!-- Total -->
                <div class="d-flex justify-content-between align-items-center my-4">
                    <strong class="total-pedido fs-5">Total: R$ <span id="totalPedido">0,00</span></strong>
                </div>

                <!-- Seção de Seleção de Endereço -->
                <div class="selecao-endereco" id="selecaoEndereco" style="display: none;">
                    <h5 class="mb-3"><i class="bi bi-geo-alt me-2"></i>Endereço de Entrega</h5>
                    
                    <?php if (empty($enderecos)): ?>
                        <div class="alert alert-warning">
                            Nenhum endereço cadastrado. Por favor, cadastre um endereço antes de finalizar o pedido.
                        </div>
                    <?php else: ?>
                        <div class="mb-3">
                            <?php foreach ($enderecos as $endereco): ?>
                                <div class="endereco-option" onclick="selecionarEndereco(this, <?= $endereco['id'] ?>)">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="endereco" 
                                            id="endereco-<?= $endereco['id'] ?>" value="<?= $endereco['id'] ?>">
                                        <label class="form-check-label" for="endereco-<?= $endereco['id'] ?>">
                                            <strong><?= esc($endereco['apelido'] ?? 'Endereço') ?></strong>
                                        </label>
                                    </div>
                                    <div class="endereco-detalhes mt-2">
                                        <p class="mb-1"><?= esc($endereco['logradouro']) ?>, <?= esc($endereco['numero']) ?></p>
                                        <p class="mb-1"><?= esc($endereco['bairro']) ?></p>
                                        <p class="mb-1"><?= esc($endereco['cidade']) ?> - <?= esc($endereco['estado']) ?></p>
                                        <p class="mb-0">CEP: <?= esc($endereco['cep']) ?></p>
                                        <?php if (!empty($endereco['complemento'])): ?>
                                            <p class="mb-0">Complemento: <?= esc($endereco['complemento']) ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn btn-primary btn-lg w-100" id="btnFinalizar" disabled>
                    <i class="bi bi-check-circle me-2"></i>Finalizar Pedido
                </button>
            </form>
        </div>
    </div>

    <!-- Rodapé -->
    <footer class="mt-5 bg-light py-3">
        <div class="container text-center">
            <small>© <?= date('Y') ?> GestorCheff - Simplificando a Gestão de Restaurantes</small>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        let pedido = [];
        const carrinhoVazio = document.getElementById('carrinhoVazio');
        const tabelaPedido = document.getElementById('tabelaPedido');
        const btnFinalizar = document.getElementById('btnFinalizar');
        const inputItens = document.getElementById('inputItens');
        const contador = document.getElementById('contadorItens');
        const enderecoSelecionado = document.getElementById('enderecoSelecionado');
        const selecaoEndereco = document.getElementById('selecaoEndereco');

        function selecionarEndereco(elemento, enderecoId) {
            // Remove a classe 'selected' de todos os endereços
            document.querySelectorAll('.endereco-option').forEach(el => {
                el.classList.remove('selected');
            });
            
            // Adiciona a classe 'selected' ao endereço clicado
            elemento.classList.add('selected');
            
            // Marca o radio button correspondente
            const radio = elemento.querySelector('input[type="radio"]');
            radio.checked = true;
            
            // Armazena o ID do endereço selecionado
            enderecoSelecionado.value = enderecoId;
            
            // Habilita o botão de finalizar
            btnFinalizar.disabled = false;
            
            // Feedback visual
            const toast = document.createElement('div');
            toast.className = 'position-fixed bottom-0 end-0 p-3';
            toast.style.zIndex = '11';
            toast.innerHTML = `
                <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header bg-success text-white">
                        <strong class="me-auto">Endereço selecionado</strong>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body">
                        Endereço de entrega definido com sucesso.
                    </div>
                </div>
            `;
            document.body.appendChild(toast);
            
            // Remove o toast após 3 segundos
            setTimeout(() => {
                toast.remove();
            }, 3000);
        }

        function adicionarItem(event, id, nome, preco, descricao) {
            event.preventDefault();
            const btn = event.currentTarget;
            const quantidade = parseInt(document.getElementById(`qtd-${id}`).value) || 1;

            const existente = pedido.find(item => item.id === id);
            if (existente) {
                existente.quantidade += quantidade;
                if (existente.quantidade > 20) existente.quantidade = 20;
            } else {
                pedido.push({ id, nome, preco, descricao, quantidade });
            }

            renderPedido();

            // Feedback visual no botão
            btn.innerHTML = '<i class="bi bi-check2 me-2"></i>Adicionado';
            btn.classList.remove('btn-primary');
            btn.classList.add('btn-success');
            setTimeout(() => {
                btn.innerHTML = `<i class="bi bi-cart-plus me-2"></i>Adicionar`;
                btn.classList.remove('btn-success');
                btn.classList.add('btn-primary');
            }, 1000);
        }

        function removerItem(id) {
            pedido = pedido.filter(item => item.id !== id);
            renderPedido();
        }

        function atualizarQuantidade(id, novaQuantidade) {
            const item = pedido.find(i => i.id === id);
            if (item) {
                if (novaQuantidade < 1) {
                    removerItem(id);
                } else {
                    item.quantidade = Math.min(novaQuantidade, 20);
                    renderPedido();
                }
            }
        }

        function renderPedido() {
            const tbody = tabelaPedido.querySelector("tbody");
            tbody.innerHTML = "";

            let total = 0;
            let totalItens = 0;

            pedido.forEach(item => {
                const subtotal = item.quantidade * item.preco;
                total += subtotal;
                totalItens += item.quantidade;

                const tr = document.createElement("tr");
                tr.innerHTML = `
                    <td>
                        <strong>${item.nome}</strong><br/>
                        <small class="text-muted">${item.descricao}</small>
                    </td>
                    <td>
                        <input type="number" min="1" max="20" value="${item.quantidade}" 
                            class="form-control form-control-sm quantidade-carrinho" 
                            onchange="atualizarQuantidade(${item.id}, parseInt(this.value))"
                            style="width: 70px;">
                    </td>
                    <td>R$ ${subtotal.toFixed(2).replace('.', ',')}</td>
                    <td class="text-end">
                        <button type="button" class="btn btn-outline-danger btn-sm btn-remove" 
                                onclick="removerItem(${item.id})" aria-label="Remover ${item.nome}">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                `;
                tbody.appendChild(tr);
            });

            document.getElementById("totalPedido").textContent = total.toFixed(2).replace('.', ',');
            contador.textContent = totalItens;
            inputItens.value = JSON.stringify(pedido.map(({id, quantidade}) => ({id, quantidade})));

            // Mostra/oculta elementos conforme necessário
            const temItens = pedido.length > 0;
            carrinhoVazio.style.display = temItens ? 'none' : 'block';
            tabelaPedido.style.display = temItens ? 'table' : 'none';
            selecaoEndereco.style.display = temItens ? 'block' : 'none';
            
            // Desabilita o botão se não tiver itens ou endereço selecionado
            btnFinalizar.disabled = !temItens || !enderecoSelecionado.value;
        }

        // Validação antes de enviar o formulário
        document.getElementById('formPedido').addEventListener('submit', function(e) {
            if (pedido.length === 0) {
                e.preventDefault();
                alert('Seu carrinho está vazio!');
                return;
            }
            
            if (!enderecoSelecionado.value) {
                e.preventDefault();
                alert('Por favor, selecione um endereço para entrega');
                return;
            }
        });
    </script>
</body>
</html>