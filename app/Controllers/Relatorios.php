<?php

namespace App\Controllers;

use App\Models\RelatoriosRestaurantesModel;
use App\Models\PedidosTimeLineModel;
use App\Models\ItensPedidoModel;
use App\Models\RestaurantesModel;

class Relatorios extends BaseController
{
    public function index()
    {
        $relatorioModel = new RelatoriosRestaurantesModel();
        
        $data = [
            'relatorios' => $relatorioModel->orderBy('receita_total', 'DESC')->findAll(),
            'estatisticas' => $relatorioModel->getEstatisticasGerais()
        ];
        
        return view('restaurantes/relatorio-restaurante', $data);
    }
    
    public function sincronizar()
    {
        $relatorioModel = new RelatoriosRestaurantesModel();
        $pedidoModel = new PedidosTimeLineModel();
        $itemPedidoModel = new ItensPedidoModel();
        $restauranteModel = new RestaurantesModel();
        
        // Obter todos os restaurantes
        $restaurantes = $restauranteModel->findAll();
        
        foreach ($restaurantes as $restaurante) {
            // Calcular estatísticas para cada restaurante
            $totalPedidos = $pedidoModel->where('restaurante_id', $restaurante['id'])->countAllResults();
            
            // Pedidos dos últimos 30 dias
            $pedidos30Dias = $pedidoModel->where('restaurante_id', $restaurante['id'])
                                        ->where('criado_em >=', date('Y-m-d', strtotime('-30 days')))
                                        ->countAllResults();
            
            // Receita total
            $receitaTotal = $pedidoModel->selectSum('valor_total')
                                      ->where('restaurante_id', $restaurante['id'])
                                      ->get()
                                      ->getRowArray()['valor_total'] ?? 0;
            
            // Receita últimos 30 dias
            $receita30Dias = $pedidoModel->selectSum('valor_total')
                                        ->where('restaurante_id', $restaurante['id'])
                                        ->where('criado_em >=', date('Y-m-d', strtotime('-30 days')))
                                        ->get()
                                        ->getRowArray()['valor_total'] ?? 0;
            
            // Avaliação média
            $avaliacaoMedia = $pedidoModel->selectAvg('avaliacao')
                                        ->where('restaurante_id', $restaurante['id'])
                                        ->where('avaliacao IS NOT NULL')
                                        ->get()
                                        ->getRowArray()['avaliacao'] ?? 0;
            
            // Total de clientes únicos
            $totalClientes = $pedidoModel->select('COUNT(DISTINCT usuario_id) as total_clientes')
                ->where('restaurante_id', $restaurante['id'])
                ->get()
                ->getRowArray()['total_clientes'] ?? 0;

            
            // Produto mais vendido
            $produtoMaisVendido = $itemPedidoModel->select('itens_cardapio.nome, COUNT(*) as total')
                                                ->join('itens_cardapio', 'itens_cardapio.id = itens_pedido.cardapio_id')
                                                ->join('pedidos', 'pedidos.id = itens_pedido.pedido_id')
                                                ->where('pedidos.restaurante_id', $restaurante['id'])
                                                ->groupBy('itens_pedido.cardapio_id')
                                                ->orderBy('total', 'DESC')
                                                ->get()
                                                ->getRowArray()['nome'] ?? 'Nenhum';
            
            // Preparar dados para atualização
            $dadosRelatorio = [
                'restaurante_id' => $restaurante['id'],
                'nome_restaurante' => $restaurante['nome'],
                'total_pedidos' => $totalPedidos,
                'pedidos_30dias' => $pedidos30Dias,
                'receita_total' => $receitaTotal,
                'receita_30dias' => $receita30Dias,
                'avaliacao_media' => $avaliacaoMedia,
                'total_clientes' => $totalClientes,
                'produto_mais_vendido' => $produtoMaisVendido,
                'atualizado_em' => date('Y-m-d H:i:s')
            ];
            
            // Verificar se já existe um relatório para este restaurante
            $relatorioExistente = $relatorioModel->where('restaurante_id', $restaurante['id'])->first();
            
            if ($relatorioExistente) {
                $relatorioModel->update($relatorioExistente['id'], $dadosRelatorio);
            } else {
                $relatorioModel->insert($dadosRelatorio);
            }
        }
        
        return redirect()->to('/relatorios')->with('success', 'Relatórios sincronizados com sucesso!');
    }
    
    public function detalhes($restaurante_id)
    {
        $relatorioModel = new RelatoriosRestaurantesModel();
        
        $data = [
            'relatorio' => $relatorioModel->getRelatorioCompleto($restaurante_id),
            'topRestaurantes' => $relatorioModel->getTopRestaurantes(5)
        ];
        
        return view('relatorios/detalhes', $data);
    }
}