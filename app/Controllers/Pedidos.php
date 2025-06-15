<?php
namespace App\Controllers;
use App\Models\ItensCardapioModel;
use App\Models\UsuariosModel; 
use App\Models\ItensPedidoModel;
use App\Models\EnderecoModel;
use App\Models\PedidosTimeLineModel;
use CodeIgniter\Controller; 

helper('status'); // Carrega seu helper com as funções

class Pedidos extends BaseController {

    /**
     * Página de pedidos do restaurante
     * 
     * @param int $restauranteId
     * @return \CodeIgniter\HTTP\ResponseInterface
     */

     public function __construct()
    {
        $this->pedidoModel = new PedidosTimeLineModel();
    }


    public function index()
    {
        // Seus pedidos normais (excluindo os enviados)
        $pedidos = $this->pedidoModel
            ->where('usuario_id', session('usuario_id'))
            ->groupStart()
                ->where('status !=', 'enviado')
                ->orWhere('status', null)
            ->groupEnd()
            ->orderBy('criado_em', 'DESC')
            ->findAll();
        
        // Pedidos enviados
        $pedidos_entrega = $this->pedidoModel
            ->where('usuario_id', session('usuario_id'))
            ->where('status', 'enviado')
            ->orderBy('envio_entrega', 'DESC')
            ->findAll();

        return view('pedidos/index', [
            'pedidos' => $pedidos,
            'pedidos_entrega' => $pedidos_entrega,
            'pager' => $this->pedidoModel->pager ?? null
        ]);
    }

    public function confirmarEntrega($id)
    {
        $pedido = $this->pedidoModel->find($id);
        
        if (!$pedido || $pedido['usuario_id'] != session('usuario_id')) {
            return redirect()->back()->with('error', 'Pedido não encontrado');
        }

        if ($pedido['status'] != 'enviado') {
            return redirect()->back()->with('error', 'Este pedido não está marcado como enviado');
        }

        $data = [
            'data_entrega' => date('Y-m-d H:i:s'),
            'status' => 'entregue',
        ];

        if ($this->pedidoModel->update($id, $data)) {
            return redirect()->back()->with('success', 'Pedido marcado como entregue com sucesso');
        }

        return redirect()->back()->with('error', 'Não foi possível confirmar a entrega');
    }


    public function salvar()
    {
        $pedidoModel   = new \App\Models\PedidosTimeLineModel();
        $itemModel     = new \App\Models\ItensCardapioModel();
        $usuarioModel  = new \App\Models\UsuariosModel();
        $enderecoModel = new \App\Models\EnderecoModel();

        $itensJson  = $this->request->getPost('itens');
        $enderecoId = $this->request->getPost('endereco_id');

        if (empty($itensJson)) {
            return redirect()->back()->with('error', 'Carrinho está vazio.');
        }

        $itens = json_decode($itensJson, true);
        if (empty($itens) || !is_array($itens)) {
            return redirect()->back()->with('error', 'Itens inválidos.');
        }

        // Pega o usuário logado
        $usuarioId = session()->get('usuario_id');
        if (!$usuarioId) {
            return redirect()->back()->with('error', 'Usuário não autenticado.');
        }

        $usuario = $usuarioModel->find($usuarioId);
        if (!$usuario) {
            return redirect()->back()->with('error', 'Usuário não encontrado.');
        }

        // Pega endereço
        $endereco = $enderecoModel->find($enderecoId);
        if (!$endereco) {
            return redirect()->back()->with('error', 'Endereço inválido.');
        }

        // Calcula o valor total
        $valorTotal = 0;
        foreach ($itens as $item) {
            $cardapio = $itemModel->find($item['id']);
            if (!$cardapio) continue;

            $valorTotal += $cardapio['preco'] * $item['quantidade'];

            $restauranteId = $cardapio['restaurante_id'];
        }

        if ($valorTotal <= 0) {
            return redirect()->back()->with('error', 'Erro ao calcular o valor total.');
        }

        // Prepara os dados para salvar o pedido
        $pedidoData = [
            'restaurante_id'      => $restauranteId, // ou dinâmico se você tiver isso no contexto
            'usuario_id'          => $usuarioId,
            'cliente_nome'        => $usuario['nome'] . ' ' . $usuario['sobrenome'],
            'cliente_telefone'    => $usuario['telefone'] ?? '',
            'cliente_endereco'    => $endereco['logradouro'] . ', ' . $endereco['numero'] . ' - ' . $endereco['bairro'] . ', ' . $endereco['cidade'] . '/' . $endereco['estado'],
            'valor_total'         => $valorTotal,
            'status'              => 'aguardando',
            'criado_em'           => date('Y-m-d H:i:s'),
            'atualizado_em'       => date('Y-m-d H:i:s'),
        ];

        if (!$pedidoModel->save($pedidoData)) {
            return redirect()->back()->with('error', 'Erro ao salvar o pedido.');
        }

        $pedidoId = $pedidoModel->getInsertID(); // pega o ID do pedido recém-salvo
        $itensPedidoModel = new \App\Models\ItensPedidoModel();

        foreach ($itens as $item) {
            $cardapio = $itemModel->find($item['id']);
            if (!$cardapio) continue;

            $itensPedidoModel->save([
                'pedido_id'      => $pedidoId,
                'cardapio_id'    => $cardapio['id'],
                'quantidade'     => $item['quantidade'],
                'preco_unitario' => $cardapio['preco'],
                'preco_total'    => $cardapio['preco'] * $item['quantidade']
            ]);
        }


        return redirect()->to('usuarios/painelUsuario')->with('success', 'Pedido criado com sucesso!');
    }


    /**
     * Rastrear pedidos do usuário
     */
    public function rastrear()
    {
        $pedidoModel = new PedidosTimeLineModel();
        $restauranteModel = new \App\Models\RestaurantesModel(); // Adicione este model
        
        $usuarioId = session()->get('usuario_id');
        if (!$usuarioId) {
            return redirect()->to('/login')->with('error', 'Você precisa estar logado');
        }

        // Busca pedidos com informações do restaurante
        $pedidos = $pedidoModel->select('pedidos.*, restaurantes.nome as restaurante_nome')
                    ->join('restaurantes', 'restaurantes.id = pedidos.restaurante_id')
                    ->where('pedidos.usuario_id', $usuarioId)
                    ->orderBy('pedidos.criado_em', 'DESC')
                    ->findAll();

        return view('usuarios/rastreio-pedidos', [
            'pedidos' => $pedidos
        ]);
    }


    public function detalhes($pedidoId)
    {
        $db = \Config\Database::connect();
        $pedidoModel = new \App\Models\PedidosTimeLineModel();

        // Busca o pedido
        $pedido = $pedidoModel->find($pedidoId);
        if (!$pedido) {
            return redirect()->back()->with('error', 'Pedido não encontrado');
        }

        // Verifica se pertence ao usuário logado
        if ($pedido['usuario_id'] != session()->get('usuario_id')) {
            return redirect()->back()->with('error', 'Acesso não autorizado');
        }

        // Query SQL nativa
        $query = "
            SELECT itens_pedido.*, itens_cardapio.nome AS item_nome
            FROM itens_pedido
            JOIN itens_cardapio ON itens_cardapio.id = itens_pedido.cardapio_id
            WHERE itens_pedido.pedido_id = ?
        ";
        $itens = $db->query($query, [$pedidoId])->getResultArray();

        return view('usuarios/detalhes-pedido', [
            'pedido' => $pedido,
            'itens'  => $itens
        ]);
    }


    public function confirmar($id)
    {
        $pedidoModel = new \App\Models\PedidosTimelineModel();
        
        // Verifica se o pedido existe e está no status aguardando
        $pedido = $pedidoModel->find($id);
        if (!$pedido || $pedido['status'] != 'aguardando') {
            return redirect()->back()->with('error', 'Pedido não encontrado ou já confirmado');
        }

        // Atualiza para status "preparando" e registra o horário
        $pedidoModel->atualizarStatus($id, 'preparando');
        
        return redirect()->back()->with('success', 'Pedido confirmado e em preparação');
    }


    public function cancelar($id)
    {
        $pedidoModel = new \App\Models\PedidosTimelineModel();
        
        // Verifica se o pedido existe e pode ser cancelado
        $pedido = $pedidoModel->find($id);
        if (!$pedido || in_array($pedido['status'], ['cancelado', 'finalizado'])) {
            return redirect()->back()->with('error', 'Pedido não encontrado ou não pode ser cancelado');
        }

        // Atualiza para status "cancelado" e registra o horário
        $pedidoModel->atualizarStatus($id, 'cancelado');
        
        return redirect()->back()->with('success', 'Pedido cancelado com sucesso');
    }


    public function finalizar($id)
    {
        $pedidoModel = new \App\Models\PedidosTimelineModel();
        
        // Verifica se o pedido existe e está no status "preparando"
        $pedido = $pedidoModel->find($id);
        if (!$pedido || $pedido['status'] != 'preparando') {
            return redirect()->back()->with('error', 'Pedido não encontrado ou não pode ser finalizado');
        }

        // Atualiza para status "finalizado" e registra o horário
        $pedidoModel->atualizarStatus($id, 'finalizado');
        
        return redirect()->back()->with('success', 'Pedido finalizado com sucesso');

    }

    public function enviar($id)
    {
        $pedidoModel = new \App\Models\PedidosTimelineModel();
        
        // Verifica se o pedido existe e está no status preparando
        $pedido = $pedidoModel->find($id);
        if (!$pedido || $pedido['status'] != 'preparando') {
            return redirect()->back()->with('error', 'Pedido não está pronto para envio');
        }

        // Atualiza para status "enviado" e registra o horário
        if ($pedidoModel->atualizarStatus($id, 'enviado')) {
            return redirect()->back()->with('success', 'Pedido marcado como enviado para entrega');
        }
        
        return redirect()->back()->with('error', 'Não foi possível atualizar o status do pedido');
    }

}
?>