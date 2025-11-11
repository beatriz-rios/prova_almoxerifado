// AQUI ESTÁ A LISTA FIXA DE USUÁRIOS
const USUARIOS_PERMITIDOS = [
    { email: 'vitor@empresa.com', senha: '123456', nome: 'Vitor' },
    { email: 'bia@empresa.com', senha: '123456', nome: 'Bia' },
    { email: 'gideao@empresa.com', senha: '123456', nome: 'Gideao' }
];

// --- Configurações e Variáveis ---
const FORMULARIO = document.getElementById('formulario-login');
const MENSAGEM_ERRO = document.getElementById('mensagem-erro');
const URL_LOGIN = 'index.html'; // Tela atual de login

const TEMPO_REDIRECIONAMENTO_MS = 4000; // 4 segundos

// --- Função que executa a regra de negócio de ERRO ---
function lidarComFalha(motivoDaFalha) {
    
    // 1. Informar ao usuário o motivo da falha
    MENSAGEM_ERRO.textContent = alert(`🚫 FALHA DE AUTENTICAÇÃO: ${motivoDaFalha} Redirecionando para a tela de login em ${TEMPO_REDIRECIONAMENTO_MS / 1000} segundos...`);
    if(MENSAGEM_ERRO) MENSAGEM_ERRO.style.display = 'block'; // Torna a mensagem visível

    // 2. Redirecionar novamente à tela de login após um pequeno atraso
    setTimeout(() => {
        window.location.replace(URL_LOGIN); 
    }, TEMPO_REDIRECIONAMENTO_MS);
}

// --- Função Principal: Tratamento do Envio do Formulário ---
// Apenas executa se o formulário existir na página atual (provavelmente login.html)
if (FORMULARIO) {
    FORMULARIO.addEventListener('submit', function(evento) {
        evento.preventDefault(); 
        
        // Assegurando que os elementos existem antes de tentar pegar o valor
        // Nota: Os IDs nos inputs de login.html são: nome-input, email-input, senha-input
        const emailInput = document.getElementById('email-input');
        const senhaInput = document.getElementById('senha-input');

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
            localStorage.setItem('usuarioLogado', JSON.stringify(usuarioEncontrado)); // NOVO: SALVA O USUÁRIO LOGADO
            alert(`Bem-vindo(a), ${usuarioEncontrado.nome}! Redirecionando...`);
            // Redireciona para a URL de sucesso (cadastro.html)
         
            
        } else {
            // 2. Caso de Falha:
            const motivo = "Credenciais inválidas. Verifique seu e-mail e senha."; 
            lidarComFalha(motivo);
        }

        // --- NOVO CÓDIGO: RESETA OS CAMPOS APÓS TENTATIVA ---
        emailInput.value = '';
        senhaInput.value = '';
        // O campo 'nome-input' também é limpo por cortesia, caso o usuário o tenha preenchido
        const nomeInput = document.getElementById('nome-input');
        if (nomeInput) {
            nomeInput.value = '';
        }
        // ---------------------------------------------------
    });
}




//ESTILIZACAO DO PERFIL DO USUARIO (Revisado para buscar do localStorage)
document.addEventListener('DOMContentLoaded', (event) => {
    // 1. Elementos do DOM
    const iconeUsuario = document.getElementById('icone-usuario');
    const infoPainel = document.getElementById('info-usuario');
    const displayNome = document.getElementById('display-nome');
    const displayEmail = document.getElementById('display-email');

    // Verifica se os elementos cruciais existem
    if (!iconeUsuario || !infoPainel || !displayNome || !displayEmail) {
        return;
    }

    // 2. Função para carregar os dados e alternar a exibição
    function atualizarEExibirInfo() {
        // Tenta buscar o usuário logado no localStorage
        const usuarioJson = localStorage.getItem('usuarioLogado');
        let usuarioLogado = null;

        if (usuarioJson) {
            try {
                // Converte a string JSON de volta para um objeto
                usuarioLogado = JSON.parse(usuarioJson);
            } catch (e) {
                console.error("Erro ao fazer parse do usuário no localStorage", e);
            }
        }

        // Define os valores para exibição
        const nome = usuarioLogado ? usuarioLogado.nome : "Usuário Desconhecido (Faça Login)";
        const email = usuarioLogado ? usuarioLogado.email : "N/A";

        // Preenche a div de informações
        displayNome.textContent = nome;
        displayEmail.textContent = email;

        // 3. Alterna a visibilidade do painel (como um "toggle")
        const isVisible = infoPainel.style.display === 'block';
        
        if (isVisible) {
            infoPainel.style.display = 'none'; // Esconde
        } else {
            infoPainel.style.display = 'block'; // Mostra
        }
    }

    // 4. Adiciona o evento de clique ao ícone
    iconeUsuario.addEventListener('click', atualizarEExibirInfo);
});