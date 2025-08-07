<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Menu;
use App\Models\User;
use App\Models\Rols;
use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        /**********************************
         * Crea el Primer rol de usuario
         **********************************/

            Rols::create(['name' => 'Administrador',]);
            Rols::create(['name' => 'Empleado',]);
            Rols::create(['name' => 'Cliente',]);
        /**********************************
         * Crea el primer usuario
         ***********************************/
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'rol_id' => 1,
        ]);

        /************************************
         * Crea las primeras categorias
         ************************************/
        Category::factory()->create(['name' => 'Bebidas Carbonatadas','status' => true]);
        Category::factory()->create(['name' => 'Cervezas','status' => true]);
        Category::factory()->create(['name' => 'Snacks','status' => true]);
        Category::factory()->create(['name' => 'Lacteos','status' => true]);

        /***********************************
         *  Crea los primeros menu de Administración
         ************************************/
        Menu::create(['nombre' => 'Dashboard','icono' => 'bi-house','url' => '/dashboard']);
        Menu::create(['nombre' => 'Categorias','icono' => 'bi-tags','url' => '/categories']);
        Menu::create(['nombre' => 'Productos','icono' => 'bi-cart','url' => '/products']);
        Menu::create(['nombre' => 'Ventas','icono' => 'bi-shop','url' => '/orders']);
        Menu::create(['nombre' => 'Compras','icono' => 'bi-cart','url' => '/orders']);
        Menu::create(['nombre' => 'Reportes','icono' => 'bi-bar-chart','url' => '/reports']);
        Menu::create(['nombre' => 'Usuarios','icono' => 'bi-user','url' => '/users']);
        Menu::create(['nombre' => 'Clientes','icono' => 'bi-persons','url' => '/roles']);
        Menu::create(['nombre' => 'Perfil','icono' => 'bi-user','url' => '/profile']);
        Menu::create(['nombre' => 'Tienda','icono' => 'bi-shop-window','url' => '/store']);
    }
}
