<?php

namespace Database\Seeders;

use App\Models\CarMake;
use App\Models\CarModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CarCatalogSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->catalog() as $makeName => $models) {
            $make = CarMake::updateOrCreate(
                ['slug' => Str::slug($makeName)],
                ['name' => $makeName]
            );

            foreach ($models as $modelName) {
                CarModel::updateOrCreate(
                    [
                        'car_make_id' => $make->id,
                        'slug' => Str::slug($modelName),
                    ],
                    ['name' => $modelName]
                );
            }
        }
    }

    private function catalog(): array
    {
        return [
            'Toyota' => ['Premio', 'Axio', 'Fielder', 'Prado', 'Land Cruiser', 'Harrier', 'Hilux', 'Vitz', 'Noah', 'Hiace', 'Wish', 'Crown', 'RAV4'],
            'Nissan' => ['X-Trail', 'Note', 'March', 'Juke', 'Navara', 'Teana', 'Patrol', 'Caravan', 'Dualis'],
            'Mazda' => ['Demio', 'CX-3', 'CX-5', 'CX-9', 'Axela', 'Atenza', 'BT-50', 'Verisa'],
            'Honda' => ['Fit', 'Vezel', 'CR-V', 'Civic', 'Accord', 'Stream', 'Insight'],
            'Subaru' => ['Forester', 'XV', 'Outback', 'Legacy', 'Impreza'],
            'Mitsubishi' => ['Pajero', 'Outlander', 'L200', 'Mirage', 'RVR', 'Galant'],
            'Suzuki' => ['Alto', 'Swift', 'Escudo', 'Vitara', 'Jimny', 'Wagon R'],
            'Isuzu' => ['D-Max', 'MU-X', 'NPR', 'FRR'],
            'Ford' => ['Ranger', 'Everest', 'Escape', 'Focus'],
            'Volkswagen' => ['Golf', 'Passat', 'Tiguan', 'Touareg', 'Amarok'],
            'Mercedes-Benz' => ['C-Class', 'E-Class', 'S-Class', 'GLE', 'GLC', 'Actros'],
            'BMW' => ['X1', 'X3', 'X5', '3 Series', '5 Series', '7 Series'],
            'Audi' => ['A3', 'A4', 'A5', 'Q3', 'Q5', 'Q7'],
            'Lexus' => ['RX', 'NX', 'LX', 'GX', 'IS', 'ES'],
            'Land Rover' => ['Discovery', 'Defender', 'Range Rover', 'Evoque'],
            'Volvo' => ['XC60', 'XC90', 'FH Trucks'],
            'Hyundai' => ['Tucson', 'Santa Fe', 'Elantra', 'Sonata', 'H100'],
            'Kia' => ['Sportage', 'Sorento', 'Rio', 'Picanto', 'Cerato'],
            'Peugeot' => ['208', '3008', '5008', 'Boxer', 'Partner'],
            'Renault' => ['Duster', 'Kwid', 'Koleos', 'Logan'],
            'Jeep' => ['Wrangler', 'Cherokee', 'Compass'],
            'Porsche' => ['Cayenne', 'Macan', 'Panamera'],
            'Daihatsu' => ['Mira', 'Tanto', 'Terios', 'Boon'],
            'Tata' => ['Xenon', 'Prima', 'LPT'],
            'Mahindra' => ['Scorpio', 'Bolero', 'Pik-Up'],
            'Hino' => ['Dutro', 'Ranger', 'Profia'],
            'Fuso' => ['Canter', 'Fighter', 'Super Great'],
            'BYD' => ['Dolphin', 'Atto 3', 'Seal'],
            'Tesla' => ['Model 3', 'Model Y', 'Model X'],
            'Nissan EV' => ['Nissan Leaf'],
            'Mini' => ['Cooper', 'Countryman'],
            'Jaguar' => ['XF', 'F-Pace'],
            'Chevrolet' => ['Captiva', 'Cruze', 'Trailblazer'],
            'Chery' => ['Tiggo Series'],
            'Geely' => ['Coolray', 'Emgrand'],
            'Proton' => ['Saga', 'X70'],
            'SsangYong' => ['Korando', 'Rexton'],
            'Opel' => ['Astra', 'Insignia'],
            'Citroen' => ['C3', 'C4', 'Berlingo'],
            'Fiat' => ['500', 'Panda', 'Doblo'],
            'Alfa Romeo' => ['Giulietta', 'Stelvio'],
            'Infiniti' => ['QX50', 'Q50'],
            'Acura' => ['MDX', 'RDX'],
            'Lincoln' => ['Navigator'],
            'Bentley' => ['Bentayga', 'Continental GT'],
            'Rolls-Royce' => ['Ghost', 'Cullinan'],
            'Ferrari' => ['Portofino', 'Roma'],
            'Lamborghini' => ['Urus', 'Huracan'],
            'Maserati' => ['Levante', 'Ghibli'],
            'McLaren' => ['720S'],
        ];
    }
}
