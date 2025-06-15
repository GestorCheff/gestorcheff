<?php

namespace App\Controllers;
use App\Models\UsuariosModel; 
use App\Models\EnderecoModel;
use CodeIgniter\Controller; 

helper('status'); // Carrega seu helper com as funções

class Usuarios extends BaseController
{
    public function index()
    {
        echo view('');
    }

    // Página de Login do Usuario
    public function login()
    {
        return view('usuarios/login-usuario');
    }
    

    // Retorna a página para cadastro
    public function cadastro()
    {
        return view('usuarios/cadastro-usuario');
    }


    public function atualizar($id)
    {
        $usuarioModel = new UsuariosModel();

        $data = $this->request->getPost([
            'nome', 'sobrenome', 'datanascimento', 'email', 'telefone'
        ]);

        // Validação simples (você pode adaptar para usar rules do CI4)
        if (!$usuarioModel->update($id, $data)) {
            return redirect()->back()->with('errors', $usuarioModel->errors());
        }

        $usuarioId = session()->get('usuario_id');

        if (!$usuarioId) {
            return redirect()->to('/usuarios/login');
        }

        $usuario = $usuarioModel->find($usuarioId);
        $email = $data['email'] ?? $usuario['email'];
        $nomeUsuario = $data['nome'] ?? $usuario['nome'];

        $this->enviar_email_confirmacao($nomeUsuario, $email);

        return redirect()->back()->with('success', 'Perfil atualizado com sucesso!');
    }



    public function informacao()
    {

        $usuarioId = session()->get('usuario_id');

        if (!$usuarioId) {
            return redirect()->to('/usuarios/login');
        }

        $usuarioModel = new UsuariosModel();
        $enderecoModel = new EnderecoModel();

        $usuario = $usuarioModel->find($usuarioId);

        if (!$usuario) {
            throw new \Exception('Usuário não encontrado');
        }

        $enderecos = $enderecoModel->where('usuario_id', $usuarioId)->findAll();

        return view('usuarios/informacao-usuario', [
            'usuario'   => $usuario,
            'enderecos' => $enderecos // <-- Enviando para a view
        ]);
    }



    public function painelUsuario()
    {

        // Pega o ID do usuário da sessão
        $usuarioId = session()->get('usuario_id');

        // Verifica se o usuário está logado
        // Se não estiver logado, redireciona para a página de login
        if (!$usuarioId) {
            return redirect()->to('/usuarios/login');
        }


        $usuarioModel = new UsuariosModel(); // veja que no seu controller é UsuariosModel (plural)
        $usuario = $usuarioModel->find($usuarioId);

        if (!$usuario) {
            throw new \Exception('Usuário não encontrado');
        }

        return view('usuarios/painel-usuario', ['usuario' => $usuario]);
    }
        



    /* ------------  CRUD USUARIO  ------------ */

    // Cadastra o Usuário no Banco de Dados
    // Cadastra o Usuário no Banco de Dados
    public function cadastrar()
    {
        // Verifica se o usuário já está logado
        $usuarios = new UsuariosModel();
    
        $validation = \Config\Services::validation();
    
        // Verifica se o usuário já está logado
        $rules = [
            'nome' => 'required|min_length[3]|max_length[50]',
            'sobrenome' => 'required|min_length[3]|max_length[50]',
            'email' => 'required|valid_email|is_unique[usuarios.email]',
            'telefone' => 'permit_empty|max_length[20]',
            'cpf' => 'required|validaCPF|is_unique[usuarios.cpf]',
            'datanascimento' => 'required|valid_date',
            'senha' => 'required|min_length[8]',
            'confirmasenha' => 'required|matches[senha]'
        ];
        
        // Define as mensagens de erro personalizadas
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        // Pega os dados do formulário
        $data = [
            'nome'            => $this->request->getPost('nome'),
            'sobrenome'       => $this->request->getPost('sobrenome'),
            'email'           => $this->request->getPost('email'),
            'telefone'        => $this->request->getPost('telefone'),
            'cpf'             => $this->request->getPost('cpf'),
            'datanascimento'  => $this->request->getPost('datanascimento'),
            'senha'           => password_hash($this->request->getPost('senha'), PASSWORD_DEFAULT), // senha criptografada
        ];

        
        $usuarios->save($data); 
        
        // Redireciona para a página de login com uma mensagem de sucesso
        return redirect()->to('/usuarios/login')->with('success', 'Usuário cadastrado com sucesso! Faça o login.');
    }




    /* ------------  LOGIN/LOGOUT  ------------ */
    public function logar()
    {
        $session = session();
        $model = new UsuariosModel();

        $email = $this->request->getPost('email');
        $senha = $this->request->getPost('senha');

        $usuario = $model->where('email', $email)->first();

        if ($usuario) {
            if (password_verify($senha, $usuario['senha'])) {
                $sessionData = [
                    'usuario_id' => $usuario['id'],
                    'nome' => $usuario['nome'],
                    'email' => $usuario['email'],
                    'logged_in' => true
                ];
                $session->set($sessionData);

                return redirect()->to('/usuarios/painelUsuario')->with('success', 'Login realizado com sucesso!');
            } else {
                return redirect()->back()->with('error', 'Senha incorreta.');
            }
        } else {
            return redirect()->back()->with('error', 'E-mail não encontrado.');
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/usuarios/login')->with('success', 'Você saiu com sucesso.');
    }



    public function editar($id)
    {
        $usuarioModel = new UsuariosModel();
        $usuario = $usuarioModel->find($id);

        if (!$usuario) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Usuário não encontrado');
        }

        
        return view('usuarios/editar', ['usuario' => $usuario]);
    }


    public function enviar_email_confirmacao($nomeUsuario, $emailDestino)
    {
        $email = \Config\Services::email();

        // Criando o corpo HTML do e-mail
        $mensagem = "
        <!DOCTYPE html>
        <html lang='pt-BR'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Perfil Atualizado</title>
            <style>
                body {
                    font-family: 'Arial', sans-serif;
                    line-height: 1.6;
                    color: #333;
                    max-width: 600px;
                    margin: 0 auto;
                    padding: 20px;
                }
                .header {
                    background-color: #4CAF50;
                    color: white;
                    padding: 20px;
                    text-align: center;
                    border-radius: 5px 5px 0 0;
                }
                .content {
                    padding: 20px;
                    background-color: #f9f9f9;
                    border-radius: 0 0 5px 5px;
                    border: 1px solid #ddd;
                    border-top: none;
                }
                .button {
                    display: inline-block;
                    padding: 10px 20px;
                    background-color: #4CAF50;
                    color: white;
                    text-decoration: none;
                    border-radius: 5px;
                    margin-top: 15px;
                }
                .footer {
                    margin-top: 20px;
                    font-size: 12px;
                    text-align: center;
                    color: #777;
                }
            </style>
        </head>
        <body>
            <div class='header'>
                <h1>Perfil Atualizado com Sucesso!</h1>
            </div>
            <div class='content'>
                <p>Olá, <strong>{$nomeUsuario}</strong>!</p>
                <p>Seus dados de perfil foram atualizados com sucesso em nosso sistema.</p>
                <p>Caso não tenha sido você quem realizou essa alteração, por favor entre em contato conosco imediatamente através deste e-mail ou pelo nosso site.</p>
                
                <p>Atenciosamente,<br>
                <strong>Equipe Gestor Cheff</strong></p>
                
                <div class='footer'>
                    <p>Este é um e-mail automático, por favor não responda diretamente a esta mensagem.</p>
                    <p>© " . date('Y') . " Gestor Cheff. Todos os direitos reservados.</p>
                </div>
            </div>
        </body>
        </html>
        ";

        $email->setFrom('gestorcheff@gmail.com', 'Gestor Cheff');
        $email->setTo($emailDestino);
        $email->setSubject('Seu perfil foi atualizado - Gestor Cheff');
        $email->setMessage($mensagem);
        $email->setMailType('html'); // Definindo como e-mail HTML

        if ($email->send()) {
            log_message('info', 'Email enviado para ' . $emailDestino);
        } else {
            log_message('error', $email->printDebugger(['headers']));
        }
    }
}
