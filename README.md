# 🌍 Gincaneiros - Jogo de Geolocalização

Gincaneiros é um jogo de geolocalização inspirado no GeoGuessr, desenvolvido com Laravel 10. Os jogadores devem adivinhar sua localização no mundo usando apenas imagens do Google Street View.

## 🎯 Funcionalidades

- **🎮 Jogo Principal**: Interface interativa com Google Street View e mapa
- **👤 Sistema de Usuários**: Autenticação completa com Laravel Breeze
- **🔑 Login Social**: Integração com Google OAuth
- **🏆 Gincanas Personalizadas**: Criação de desafios customizados
- **📊 Sistema de Rankings**: Acompanhe pontuações e progressos
- **📱 Interface Responsiva**: Funciona perfeitamente em mobile e desktop
- **🎨 Design Moderno**: Interface limpa e intuitiva

## 🚀 Tecnologias Utilizadas

- **Backend**: Laravel 10
- **Frontend**: Blade Templates + Tailwind CSS
- **Autenticação**: Laravel Breeze + Socialite
- **Mapas**: Google Maps API + Street View
- **Banco de Dados**: MySQL
- **Build Tool**: Vite
- **Alertas**: SweetAlert2

## ⚙️ Instalação

### Pré-requisitos
- PHP 8.1+
- Composer
- Node.js & NPM
- MySQL
- Conta Google Cloud (para APIs de Maps)

### Passo a passo

1. **Clone o repositório**
```bash
git clone https://github.com/rodrigojp2005/ginca10.git
cd ginca10
```

2. **Instale as dependências PHP**
```bash
composer install
```

3. **Instale as dependências JavaScript**
```bash
npm install
```

4. **Configure o ambiente**
```bash
cp .env.example .env
php artisan key:generate
```

5. **Configure o banco de dados**
Edite o arquivo `.env` com suas configurações:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ginca10
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```

6. **Configure as APIs do Google**
No arquivo `.env`, adicione suas chaves:
```env
GOOGLE_MAPS_API_KEY=sua_chave_google_maps
GOOGLE_CLIENT_ID=seu_client_id_oauth
GOOGLE_CLIENT_SECRET=seu_client_secret_oauth
```

7. **Execute as migrações**
```bash
php artisan migrate
```

8. **Compile os assets**
```bash
npm run dev
```

9. **Inicie o servidor**
```bash
php artisan serve
```

Acesse: `http://localhost:8000`

## 🎮 Como Jogar

1. **Observe** a imagem do Street View
2. **Procure pistas**: placas, arquitetura, vegetação, idioma
3. **Clique em "JOGAR"** para abrir o mapa
4. **Marque sua localização** no mapa
5. **Confirme seu palpite** e veja sua pontuação!

### 📊 Sistema de Pontuação
- Comece com 1000 pontos
- Perca 200 pontos a cada erro
- Acerte dentro de 10km para vencer!
- Você tem 5 tentativas por rodada

## 🏗️ Estrutura do Projeto

```
app/
├── Http/Controllers/
│   ├── GincanaController.php    # Gerenciamento de gincanas
│   ├── GameController.php       # Lógica do jogo
│   ├── RankingController.php    # Sistema de rankings
│   └── Auth/SocialiteController.php # OAuth Google
├── Models/
│   ├── User.php                 # Modelo de usuários
│   ├── Gincana.php             # Modelo de gincanas
│   ├── Participacao.php        # Participações em gincanas
│   └── GincanaLocal.php        # Locais das gincanas
resources/
├── views/
│   ├── layouts/                # Layouts principais
│   ├── gincana/               # Views das gincanas
│   └── welcome.blade.php      # Página principal do jogo
├── css/
│   └── game.css               # Estilos do jogo
└── js/
    └── game.js                # Lógica JavaScript do jogo
```

## 🗄️ Banco de Dados

### Principais Tabelas
- **users**: Usuários do sistema
- **gincanas**: Gincanas criadas pelos usuários
- **participacoes**: Participações dos usuários nas gincanas
- **gincana_locais**: Locais adicionais das gincanas

## 🔧 Configuração do Google APIs

### Google Maps API
1. Acesse o [Google Cloud Console](https://console.cloud.google.com/)
2. Crie um projeto ou selecione um existente
3. Ative as APIs: Maps JavaScript API, Street View Static API
4. Crie credenciais (API Key)
5. Configure restrições de domínio

### Google OAuth
1. No mesmo projeto, vá em "Credenciais"
2. Crie credenciais OAuth 2.0
3. Configure URLs autorizadas:
   - Origem: `http://localhost:8000`
   - Redirecionamento: `http://localhost:8000/auth/google/callback`

## 🤝 Contribuindo

1. Faça um Fork do projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## 📝 Licença

Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

## 👨‍💻 Desenvolvedor

**Rodrigo** - [@rodrigojp2005](https://github.com/rodrigojp2005)

## 🙏 Agradecimentos

- Inspirado no conceito do GeoGuessr
- Laravel Framework
- Google Maps Platform
- Comunidade open source

---

**⭐ Se este projeto te ajudou, deixe uma estrela!**
