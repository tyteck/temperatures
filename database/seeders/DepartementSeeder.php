<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Departement;
use Illuminate\Database\Seeder;

class DepartementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['code_insee' => '01', 'nom' => 'Ain'],
            ['code_insee' => '02', 'nom' => 'Aisne'],
            ['code_insee' => '03', 'nom' => 'Allier'],
            ['code_insee' => '04', 'nom' => 'Alpes-de-Haute-Provence'],
            ['code_insee' => '05', 'nom' => 'Hautes-Alpes'],
            ['code_insee' => '06', 'nom' => 'Alpes-Maritimes'],
            ['code_insee' => '07', 'nom' => 'Ardèche'],
            ['code_insee' => '08', 'nom' => 'Ardennes'],
            ['code_insee' => '09', 'nom' => 'Ariège'],
            ['code_insee' => '10', 'nom' => 'Aube'],
            ['code_insee' => '12', 'nom' => 'Aveyron'],
            ['code_insee' => '13', 'nom' => 'Bouches-du-Rhône'],
            ['code_insee' => '14', 'nom' => 'Calvados'],
            ['code_insee' => '15', 'nom' => 'Cantal'],
            ['code_insee' => '16', 'nom' => 'Charente'],
            ['code_insee' => '17', 'nom' => 'Charente-Maritime'],
            ['code_insee' => '18', 'nom' => 'Cher'],
            ['code_insee' => '19', 'nom' => 'Corrèze'],
            ['code_insee' => '2A', 'nom' => 'Corse-du-Sud'],
            ['code_insee' => '2B', 'nom' => 'Haute-Corse'],
            ['code_insee' => '21', 'nom' => "Côte-d'Or"],
            ['code_insee' => '22', 'nom' => "Côtes-d'Armor"],
            ['code_insee' => '23', 'nom' => 'Creuse'],
            ['code_insee' => '24', 'nom' => 'Dordogne'],
            ['code_insee' => '25', 'nom' => 'Doubs'],
            ['code_insee' => '26', 'nom' => 'Drôme'],
            ['code_insee' => '27', 'nom' => 'Eure'],
            ['code_insee' => '28', 'nom' => 'Eure-et-Loir'],
            ['code_insee' => '29', 'nom' => 'Finistère'],
            ['code_insee' => '30', 'nom' => 'Gard'],
            ['code_insee' => '31', 'nom' => 'Haute-Garonne'],
            ['code_insee' => '32', 'nom' => 'Gers'],
            ['code_insee' => '33', 'nom' => 'Gironde'],
            ['code_insee' => '34', 'nom' => 'Hérault'],
            ['code_insee' => '35', 'nom' => 'Ille-et-Vilaine'],
            ['code_insee' => '36', 'nom' => 'Indre'],
            ['code_insee' => '37', 'nom' => 'Indre-et-Loire'],
            ['code_insee' => '38', 'nom' => 'Isère'],
            ['code_insee' => '39', 'nom' => 'Jura'],
            ['code_insee' => '40', 'nom' => 'Landes'],
            ['code_insee' => '41', 'nom' => 'Loir-et-Cher'],
            ['code_insee' => '42', 'nom' => 'Loire'],
            ['code_insee' => '43', 'nom' => 'Haute-Loire'],
            ['code_insee' => '44', 'nom' => 'Loire-Atlantique'],
            ['code_insee' => '45', 'nom' => 'Loiret'],
            ['code_insee' => '46', 'nom' => 'Lot'],
            ['code_insee' => '47', 'nom' => 'Lot-et-Garonne'],
            ['code_insee' => '48', 'nom' => 'Lozère'],
            ['code_insee' => '49', 'nom' => 'Maine-et-Loire'],
            ['code_insee' => '50', 'nom' => 'Manche'],
            ['code_insee' => '51', 'nom' => 'Marne'],
            ['code_insee' => '52', 'nom' => 'Haute-Marne'],
            ['code_insee' => '53', 'nom' => 'Mayenne'],
            ['code_insee' => '54', 'nom' => 'Meurthe-et-Moselle'],
            ['code_insee' => '55', 'nom' => 'Meuse'],
            ['code_insee' => '56', 'nom' => 'Morbihan'],
            ['code_insee' => '57', 'nom' => 'Moselle'],
            ['code_insee' => '58', 'nom' => 'Nièvre'],
            ['code_insee' => '59', 'nom' => 'Nord'],
            ['code_insee' => '60', 'nom' => 'Oise'],
            ['code_insee' => '61', 'nom' => 'Orne'],
            ['code_insee' => '62', 'nom' => 'Pas-de-Calais'],
            ['code_insee' => '63', 'nom' => 'Puy-de-Dôme'],
            ['code_insee' => '64', 'nom' => 'Pyrénées-Atlantiques'],
            ['code_insee' => '65', 'nom' => 'Hautes-Pyrénées'],
            ['code_insee' => '66', 'nom' => 'Pyrénées-Orientales'],
            ['code_insee' => '67', 'nom' => 'Bas-Rhin'],
            ['code_insee' => '68', 'nom' => 'Haut-Rhin'],
            ['code_insee' => '69', 'nom' => 'Rhône'],
            ['code_insee' => '70', 'nom' => 'Haute-Saône'],
            ['code_insee' => '71', 'nom' => 'Saône-et-Loire'],
            ['code_insee' => '72', 'nom' => 'Sarthe'],
            ['code_insee' => '73', 'nom' => 'Savoie'],
            ['code_insee' => '74', 'nom' => 'Haute-Savoie'],
            ['code_insee' => '75', 'nom' => 'Paris'],
            ['code_insee' => '76', 'nom' => 'Seine-Maritime'],
            ['code_insee' => '77', 'nom' => 'Seine-et-Marne'],
            ['code_insee' => '78', 'nom' => 'Yvelines'],
            ['code_insee' => '79', 'nom' => 'Deux-Sèvres'],
            ['code_insee' => '80', 'nom' => 'Somme'],
            ['code_insee' => '81', 'nom' => 'Tarn'],
            ['code_insee' => '82', 'nom' => 'Tarn-et-Garonne'],
            ['code_insee' => '83', 'nom' => 'Var'],
            ['code_insee' => '84', 'nom' => 'Vaucluse'],
            ['code_insee' => '85', 'nom' => 'Vendée'],
            ['code_insee' => '86', 'nom' => 'Vienne'],
            ['code_insee' => '87', 'nom' => 'Haute-Vienne'],
            ['code_insee' => '88', 'nom' => 'Vosges'],
            ['code_insee' => '89', 'nom' => 'Yonne'],
            ['code_insee' => '90', 'nom' => 'Territoire de Belfort'],
            ['code_insee' => '91', 'nom' => 'Essonne'],
            ['code_insee' => '92', 'nom' => 'Hauts-de-Seine'],
            ['code_insee' => '93', 'nom' => 'Seine-Saint-Denis'],
            ['code_insee' => '94', 'nom' => 'Val-de-Marne'],
            ['code_insee' => '95', 'nom' => "Val-d'Oise"],
            ['code_insee' => '971', 'nom' => 'Guadeloupe'],
            ['code_insee' => '972', 'nom' => 'Martinique'],
            ['code_insee' => '973', 'nom' => 'Guyane'],
            ['code_insee' => '974', 'nom' => 'La Réunion'],
        ];

        $index = 1;
        $data = array_map(
            function ($item) use (&$index) {
                return array_merge($item, [
                    'id' => $index++,
                    'created_at' => now(),
                ]);
            },
            $data
        );
        Departement::insert($data);
    }
}
