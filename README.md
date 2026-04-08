PROCESSO PARA RODAR O PROJETO:


composer install
cp .env.example .env
# preencher .env com os dados reais
php artisan key:generate
php artisan migrate
