// AQUI ESTÁ A LISTA FIXA DE USUÁRIOS
const USUARIOS_PERMITIDOS = [
    { email: 'admin@empresa.com', senha: '123456', nome: 'Administrador' },
    { email: 'joao@empresa.com', senha: 'senha123', nome: 'João' },
    { email: 'maria@empresa.com', senha: 'mudar123', nome: 'Maria' }
];

// --- Configurações e Variáveis ---
const FORMULARIO = document.getElementById('formulario-login');
const MENSAGEM_ERRO = document.getElementById('mensagem-erro');
const URL_LOGIN = 'index.html'; 
const URL_SUCESSO = 'http://127.0.0.1:5500/cadastro_materiais/cadastro.html'; 
const TEMPO_REDIRECIONAMENTO_MS = 4000; // 4 segundos

// --- Função que executa a regra de negócio de ERRO ---
function lidarComFalha(motivoDaFalha) {
    
    // 1. Informar ao usuário o motivo da falha
    if (MENSAGEM_ERRO) {
        MENSAGEM_ERRO.style.backgroundColor = 'red'; // Cor de fundo para Erro
        MENSAGEM_ERRO.textContent =` 🚫 FALHA DE AUTENTICAÇÃO: ${motivoDaFalha} Redirecionando para a tela de login em ${TEMPO_REDIRECIONAMENTO_MS / 1000} segundos...`;
        MENSAGEM_ERRO.style.display = 'block'; // Torna a mensagem visível
    }

    // 2. Redirecionar novamente à tela de login após um pequeno atraso
    setTimeout(() => {
        window.location.replace(URL_LOGIN); 
    }, TEMPO_REDIRECIONAMENTO_MS);
}

// --- Função Principal: Tratamento do Envio do Formulário ---
if (FORMULARIO) {
    FORMULARIO.addEventListener('submit', function(evento) {
        evento.preventDefault(); 
        
        // 🛑 CORREÇÃO 1: Usando os IDs corretos do HTML ('email-input' e 'senha-input')
        const emailInput = document.getElementById('email-input');
        const senhaInput = document.getElementById('senha-input');

        if (!emailInput || !senhaInput) {
            console.error("Campos de email/senha não encontrados no DOM. Verifique os IDs 'email-input' e 'senha-input'.");
            return;
        }
        
        const emailDigitado = emailInput.value;
        const senhaDigitada = senhaInput.value;

        // NOVO CÓDIGO DE VERIFICAÇÃO COM A LISTA:
        const usuarioEncontrado = USUARIOS_PERMITIDOS.find(usuario => 
            usuario.email === emailDigitado && usuario.senha === senhaDigitada
        );
 if (usuarioEncontrado) {
            // 1. Caso de Sucesso:
            localStorage.setItem('usuarioLogado', JSON.stringify(usuarioEncontrado)); // NOVO: SALVA O USUÁRIO LOGADO
            alert(`Bem-vindo(a), ${usuarioEncontrado.nome}!`+` Redirecionando...`);
            // Redireciona para a URL de sucesso (cadastro.html)
            window.location.href = URL_SUCESSO; 
            
        } else {
            // 2. Caso de Falha:
            const motivo = "Credenciais inválidas. Verifique seu e-mail e senha."; 
            lidarComFalha(motivo);
        }
    });
}


// ----------------------------------------------------------------------
// ESTILIZACAO DO PERFIL DO USUARIO (Lógica para o painel info-usuario)
// ----------------------------------------------------------------------
document.addEventListener('DOMContentLoaded', (event) => {
    // 1. Elementos do DOM
    const iconeUsuario = document.getElementById('icone-usuario');
    const infoPainel = document.getElementById('info-usuario');
    // Estes IDs são lidos para preencher o painel, não para o login em si:
    const nomeInput = document.getElementById('nome-input');
    const emailInput = document.getElementById('email-input');
    
    const displayNome = document.getElementById('display-nome');
    const displayEmail = document.getElementById('display-email');

    // 2. Função para carregar e exibir os dados
    function atualizarEExibirInfo() {
        // Pega os valores atuais dos campos de input
        // Uso de Nullish Coalescing (||) e verificação de existência para robustez
        const nome = nomeInput ? nomeInput.value : "Não informado";
        const email = emailInput ? emailInput.value : "Não informado";
        
        // Preenche a div de informações
        if (displayNome) displayNome.textContent = nome;
        if (displayEmail) displayEmail.textContent = email;

        // 3. Alterna a visibilidade do painel (como um "toggle")
        if (infoPainel) {
            if (infoPainel.style.display === 'block') {
                infoPainel.style.display = 'none'; // Esconde
            } else {
                infoPainel.style.display = 'block'; // Mostra
            }
        }
    }

    // 4. Adiciona o evento de clique ao ícone
    if (iconeUsuario) {
        iconeUsuario.addEventListener('click', atualizarEExibirInfo);
    }
});