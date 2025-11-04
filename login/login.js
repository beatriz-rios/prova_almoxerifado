// AQUI ESTÁ A LISTA FIXA DE USUÁRIOS
const USUARIOS_PERMITIDOS = [
    { email: 'admin@empresa.com', senha: '123456', nome: 'Administrador' },
    { email: 'joao@empresa.com', senha: 'senha123', nome: 'João' },
    { email: 'maria@empresa.com', senha: 'mudar123', nome: 'Maria' }
];

// --- Configurações e Variáveis ---
const FORMULARIO = document.getElementById('formulario-login');
const MENSAGEM_ERRO = document.getElementById('mensagem-erro');
const URL_LOGIN = 'index.html'; // Tela atual de login
const URL_SUCESSO = 'http://127.0.0.1:5500/cadastro_materiais/cadastro.html'; // Redirecionar para a página do menu
const TEMPO_REDIRECIONAMENTO_MS = 4000; // 4 segundos

// --- Função que executa a regra de negócio de ERRO ---
function lidarComFalha(motivoDaFalha) {
    
    // 1. Informar ao usuário o motivo da falha
    MENSAGEM_ERRO.textContent = `🚫 FALHA DE AUTENTICAÇÃO: ${motivoDaFalha} Redirecionando para a tela de login em ${TEMPO_REDIRECIONAMENTO_MS / 1000} segundos...`;
    MENSAGEM_ERRO.style.display = 'block'; // Torna a mensagem visível

    // 2. Redirecionar novamente à tela de login após um pequeno atraso
    setTimeout(() => {
        window.location.replace(URL_LOGIN); 
    }, TEMPO_REDIRECIONAMENTO_MS);
}

// --- Função Principal: Tratamento do Envio do Formulário ---
FORMULARIO.addEventListener('submit', function(evento) {
    evento.preventDefault(); 
    
    // Assegurando que os elementos existem antes de tentar pegar o valor
    const emailInput = document.getElementById('email');
    const senhaInput = document.getElementById('senha');

    if (!emailInput || !senhaInput) {
        console.error("Campos de email/senha não encontrados no DOM.");
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
        alert(`Bem-vindo(a), ${usuarioEncontrado.nome}! Redirecionando...`);
        // Redireciona para a URL de sucesso (menu.html, ajustado para o seu nav)
        window.location.href = URL_SUCESSO; 
        
    } else {
        // 2. Caso de Falha:
        const motivo = "Credenciais inválidas. Verifique seu e-mail e senha."; 
        lidarComFalha(motivo);
    }
});