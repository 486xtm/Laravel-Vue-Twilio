<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Auth;

class ReadyMessagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {   
        $user = Auth::user();

        DB::table('ready_messages')->insert([
            'title' => 'Mensagem de abertura',
            'message' => 'Olá [NOME_CONTATO],

            Agradeço seu interesse em nossos produtos. Meu nome é [NOME_VENDEDOR] e sou da Porto Brasil Consórcios.
            
            Estou à disposição para lhe dar mais informações sobre consórcios.
            Podemos conversar por aqui?
            
            Abraço!            
            [ASSINATURA_USUARIO]',
            'user_id' => $user->id,
            'status_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
