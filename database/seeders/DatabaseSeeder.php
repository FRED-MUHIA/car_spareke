<?php

namespace Database\Seeders;

use App\Models\CarMake;
use App\Models\CarModel;
use App\Models\Category;
use App\Models\Garage;
use App\Models\PricingPlan;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create(['name' => 'Marketplace Admin', 'email' => 'admin@spares.test', 'phone' => '+254700000001', 'location' => 'Nairobi', 'role' => 'admin', 'email_verified_at' => now(), 'password' => Hash::make('password')]);
        $seller = User::create(['name' => 'AutoHub Dealers', 'email' => 'seller@spares.test', 'phone' => '+254700000002', 'location' => 'Kirinyaga Road, Nairobi', 'role' => 'seller', 'email_verified_at' => now(), 'password' => Hash::make('password')]);

        $categories = collect([
            ['Engine Parts', 'Pistons, mounts, belts, sensors, pumps', '⚙️'],
            ['Suspension Parts', 'Shocks, arms, bushes, stabilizers', '🛞'],
            ['Brake System', 'Pads, discs, calipers, cylinders', '🛑'],
            ['Body Parts', 'Doors, bumpers, bonnets, mirrors', '🚗'],
            ['Electrical Parts', 'Alternators, starters, ECU, wiring', '🔌'],
            ['Transmission Parts', 'Gearboxes, clutches, shafts', '🔧'],
            ['Tyres & Wheels', 'Tyres, rims, wheel caps', '🏁'],
            ['Lights', 'Headlights, tail lights, fog lamps', '💡'],
            ['Interior Parts', 'Seats, dashboards, consoles', '💺'],
            ['Cooling System', 'Radiators, fans, thermostats', '❄️'],
            ['Batteries', 'Car batteries and terminals', '🔋'],
            ['Service Parts', 'Filters, plugs, oils, service kits', '🧰'],
        ])->mapWithKeys(fn ($item) => [Str::slug($item[0]) => Category::create([
            'name' => $item[0],
            'slug' => Str::slug($item[0]),
            'description' => $item[1],
            'icon' => $item[2],
            'is_featured' => true,
        ])]);

        $makes = collect(['Toyota' => ['Corolla', 'Fielder', 'Prado', 'Vitz'], 'Nissan' => ['X-Trail', 'Note', 'Navara'], 'Subaru' => ['Forester', 'Impreza', 'Legacy'], 'Mazda' => ['Demio', 'Axela', 'CX-5'], 'Mitsubishi' => ['Outlander', 'Pajero', 'Lancer']])
            ->mapWithKeys(function ($models, $makeName) {
                $make = CarMake::create(['name' => $makeName, 'slug' => Str::slug($makeName)]);
                foreach ($models as $modelName) {
                    CarModel::create(['car_make_id' => $make->id, 'name' => $modelName, 'slug' => Str::slug($modelName)]);
                }

                return [$makeName => $make->load('models')];
            });

        $this->call(CarCatalogSeeder::class);

        $shop = Shop::create([
            'user_id' => $seller->id,
            'name' => 'AutoHub Spare Parts',
            'slug' => 'autohub-spare-parts',
            'logo_path' => 'https://images.unsplash.com/photo-1486006920555-c77dcf18193c?auto=format&fit=crop&w=300&q=80',
            'banner_path' => 'https://images.unsplash.com/photo-1517524008697-84bbe3c3fd98?auto=format&fit=crop&w=900&q=80',
            'location' => 'Kirinyaga Road, Nairobi',
            'phone' => '+254700000002',
            'whatsapp' => '+254700000002',
            'email' => 'sales@autohub.test',
            'description' => 'Dealer in genuine Japanese vehicle spares, service kits, body parts, and electrical components.',
            'is_featured' => true,
            'status' => 'active',
        ]);

        $shops = collect([$shop]);
        foreach (['Nakuru Motor Spares', 'Mombasa Auto Parts', 'Eldoret Gearbox Centre'] as $index => $name) {
            $shops->push(Shop::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'logo_path' => 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?auto=format&fit=crop&w=300&q=80',
                'location' => ['Nakuru CBD', 'Mombasa Island', 'Eldoret Town'][$index],
                'phone' => '+25471122233'.$index,
                'whatsapp' => '+25471122233'.$index,
                'description' => 'Trusted spare parts shop with active listings and fast customer response.',
                'is_featured' => true,
                'status' => 'active',
            ]));
        }

        $productRows = [
            ['Toyota Prado Complete Headlight', 'Lights', 'Toyota', 'Prado', 'Headlight Assembly', 2014, 2020, 'Used', 28500, 'Nairobi', 'https://images.unsplash.com/photo-1503736334956-4c8f8e92946d?auto=format&fit=crop&w=900&q=80'],
            ['Subaru Forester Front Shock Absorbers', 'Suspension Parts', 'Subaru', 'Forester', 'Shock Absorber', 2013, 2018, 'New', 18000, 'Nakuru', 'https://images.unsplash.com/photo-1600705722908-bab8819f2992?auto=format&fit=crop&w=900&q=80'],
            ['Mazda Axela Front Brake Pads', 'Brake System', 'Mazda', 'Axela', 'Brake Pads', 2012, 2019, 'New', 6500, 'Mombasa', 'https://images.unsplash.com/photo-1615906655593-ad0386982a0f?auto=format&fit=crop&w=900&q=80'],
            ['Nissan X-Trail Alternator', 'Electrical Parts', 'Nissan', 'X-Trail', 'Alternator', 2008, 2015, 'Refurbished', 14500, 'Nairobi', 'https://images.unsplash.com/photo-1619642751034-765dfdf7c58e?auto=format&fit=crop&w=900&q=80'],
            ['Toyota Fielder Rear Bumper', 'Body Parts', 'Toyota', 'Fielder', 'Rear Bumper', 2010, 2017, 'Used', 16000, 'Eldoret', 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=900&q=80'],
            ['Mitsubishi Pajero Radiator', 'Cooling System', 'Mitsubishi', 'Pajero', 'Radiator', 2007, 2016, 'New', 22000, 'Nairobi', 'https://images.unsplash.com/photo-1607860108855-64acf2078ed9?auto=format&fit=crop&w=900&q=80'],
            ['Toyota Vitz Service Kit', 'Service Parts', 'Toyota', 'Vitz', 'Service Kit', 2011, 2020, 'New', 4800, 'Kisumu', 'https://images.unsplash.com/photo-1486262715619-67b85e0b08d3?auto=format&fit=crop&w=900&q=80'],
            ['Subaru Impreza Gearbox', 'Transmission Parts', 'Subaru', 'Impreza', 'Automatic Gearbox', 2009, 2014, 'Used', 75000, 'Nairobi', 'https://images.unsplash.com/photo-1493238792000-8113da705763?auto=format&fit=crop&w=900&q=80'],
        ];

        foreach ($productRows as $row) {
            [$title, $category, $make, $model, $type, $from, $to, $condition, $price, $location, $image] = $row;
            Product::create([
                'user_id' => $seller->id,
                'shop_id' => $shops->random()->id,
                'category_id' => $categories[Str::slug($category)]->id,
                'car_make_id' => $makes[$make]->id,
                'car_model_id' => $makes[$make]->models->firstWhere('name', $model)->id,
                'title' => $title,
                'slug' => Str::slug($title),
                'part_type' => $type,
                'year_from' => $from,
                'year_to' => $to,
                'condition' => $condition,
                'price' => $price,
                'location' => $location,
                'images' => [$image, $image],
                'description' => "Clean {$condition} {$type} for {$make} {$model}. Tested before dispatch and ready for pickup or delivery.",
                'seller_name' => $seller->name,
                'seller_phone' => $seller->phone,
                'seller_whatsapp' => $seller->phone,
                'is_featured' => true,
                'status' => 'active',
            ]);
        }

        foreach ([
            ['Torque Masters Garage', 'Industrial Area, Nairobi', 'https://images.unsplash.com/photo-1613214149922-f1809c99b414?auto=format&fit=crop&w=1200&q=80', ['Diagnostics', 'Engine Repair', 'Suspension', 'Service'], ['Toyota', 'Nissan', 'Subaru'], 4.8, 118],
            ['Coastline Auto Clinic', 'Mombasa', 'https://images.unsplash.com/photo-1486006920555-c77dcf18193c?auto=format&fit=crop&w=1200&q=80', ['AC Repair', 'Body Work', 'Electrical'], [], 4.6, 74],
            ['Rift Valley Mechanics', 'Nakuru', 'https://images.unsplash.com/photo-1599256872237-5dcc0fbe9668?auto=format&fit=crop&w=1200&q=80', ['Brake Service', 'Transmission', 'Recovery'], ['Mercedes-Benz', 'BMW', 'Volkswagen'], 4.7, 93],
        ] as $garage) {
            Garage::create([
                'name' => $garage[0],
                'slug' => Str::slug($garage[0]),
                'location' => $garage[1],
                'phone' => '+254722100200',
                'whatsapp' => '+254722100200',
                'image_url' => $garage[2],
                'services' => $garage[3],
                'specialization_brands' => $garage[4],
                'rating' => $garage[5],
                'review_count' => $garage[6],
                'description' => 'Verified garage with experienced mechanics and fast service booking.',
                'is_featured' => true,
            ]);
        }

        foreach ([
            ['Free Listing', 0, 5, ['5 active listings', 'Basic seller profile', 'Direct calls and WhatsApp inquiries'], false, 'Start Free'],
            ['Starter Package', 500, 15, ['15 active listings', 'Seller dashboard', 'Direct calls and WhatsApp inquiries', 'Buyer inquiry notifications'], false, 'Choose Starter'],
            ['Dealer Package', 2500, 50, ['50 active listings', 'Shop profile', 'Featured products', 'Inquiry dashboard'], true, 'Choose Dealer'],
            ['Premium Shop Package', 6500, null, ['Unlimited listings', 'Promoted ads', 'Premium shop placement', 'Priority support'], false, 'Go Premium'],
        ] as $plan) {
            PricingPlan::updateOrCreate(
                ['slug' => Str::slug($plan[0])],
                [
                    'name' => $plan[0],
                    'price' => $plan[1],
                    'listing_limit' => $plan[2],
                    'features' => $plan[3],
                    'is_featured' => $plan[4],
                    'cta_label' => $plan[5],
                ]
            );
        }
    }
}
