<?php

namespace Database\Seeders;

use App\Models\Criterion;
use App\Models\Menu;
use App\Models\MenuEvaluation;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all criteria for evaluation
        $criteria = Criterion::all()->keyBy('kode');

        // Generate 150 diverse Indonesian menu items
        $menus = $this->generateMenus();

        foreach ($menus as $menuData) {
            // Extract additional data for evaluation
            $distance = $menuData['distance'];
            $portionSize = $menuData['portion_size'];
            $serviceSpeed = $menuData['service_speed'];
            $tasteRating = $menuData['taste_rating'];
            
            unset($menuData['distance'], $menuData['portion_size'], $menuData['service_speed'], $menuData['taste_rating']);

            // Create the menu
            $menu = Menu::create($menuData);

            // Create comprehensive evaluations for all 8 criteria
            $this->createEvaluations($menu, $criteria, $menuData['price'], $distance, $portionSize, $serviceSpeed, $tasteRating);
        }
    }

    /**
     * Generate 150 diverse Indonesian menu items
     * 
     * @return array
     */
    private function generateMenus(): array
    {
        $menus = [];
        
        // Category 1: Warung Street Food (40 items) - budget-friendly, closer to campus
        $warungVendors = ['Warung Bu Lastri', 'Warung Mas Budi', 'Warung Sederhana', 'Warung Pak Joko', 
                          'Warung Bu Endang', 'Warung Makan Berkah', 'Warung Tegal Ayu', 'Warung Nasi Ibu'];
        
        $warungMenus = [
            ['name' => 'Nasi Goreng Kampung', 'price' => [10000, 15000], 'desc' => 'Nasi goreng dengan telur, ayam suwir, dan kerupuk'],
            ['name' => 'Mie Ayam Bakso', 'price' => [9000, 13000], 'desc' => 'Mie ayam dengan bakso dan pangsit goreng'],
            ['name' => 'Soto Ayam', 'price' => [11000, 14000], 'desc' => 'Soto ayam kuah kuning dengan nasi dan kerupuk'],
            ['name' => 'Nasi Pecel', 'price' => [7000, 10000], 'desc' => 'Nasi dengan sayuran rebus dan sambal pecel kacang'],
            ['name' => 'Nasi Campur', 'price' => [12000, 16000], 'desc' => 'Nasi dengan lauk ayam goreng, telur, sambal, dan sayur'],
            ['name' => 'Nasi Rames', 'price' => [11000, 15000], 'desc' => 'Nasi dengan berbagai lauk pauk pilihan'],
            ['name' => 'Mie Goreng Jawa', 'price' => [9000, 12000], 'desc' => 'Mie goreng bumbu jawa dengan sayuran'],
            ['name' => 'Nasi Kucing Komplit', 'price' => [6000, 9000], 'desc' => 'Nasi bungkus kecil dengan oseng tempe dan teri'],
            ['name' => 'Pecel Lele', 'price' => [10000, 14000], 'desc' => 'Lele goreng dengan sambal dan lalapan'],
            ['name' => 'Ayam Penyet', 'price' => [12000, 16000], 'desc' => 'Ayam goreng geprek dengan sambal terasi'],
            ['name' => 'Nasi Gudeg Jogja', 'price' => [10000, 14000], 'desc' => 'Nasi dengan gudeg, ayam, dan telur'],
            ['name' => 'Bakso Urat', 'price' => [10000, 13000], 'desc' => 'Bakso urat dengan mie dan kuah kaldu'],
            ['name' => 'Gado-Gado', 'price' => [8000, 12000], 'desc' => 'Sayuran dengan saus kacang dan lontong'],
            ['name' => 'Nasi Uduk', 'price' => [9000, 13000], 'desc' => 'Nasi uduk dengan ayam goreng dan sambal'],
            ['name' => 'Sate Ayam', 'price' => [11000, 15000], 'desc' => 'Sate ayam dengan bumbu kacang dan lontong'],
            ['name' => 'Nasi Liwet', 'price' => [10000, 14000], 'desc' => 'Nasi liwet dengan ayam suwir dan sambal goreng'],
            ['name' => 'Lontong Sayur', 'price' => [8000, 11000], 'desc' => 'Lontong dengan sayur lodeh dan sambal goreng'],
            ['name' => 'Bubur Ayam', 'price' => [7000, 11000], 'desc' => 'Bubur dengan ayam suwir, kacang, dan kerupuk'],
            ['name' => 'Nasi Goreng Sosis', 'price' => [11000, 14000], 'desc' => 'Nasi goreng dengan sosis dan telur mata sapi'],
            ['name' => 'Mie Ayam Jamur', 'price' => [10000, 13000], 'desc' => 'Mie ayam dengan jamur dan pangsit rebus'],
        ];

        foreach ($warungMenus as $idx => $item) {
            $vendor = $warungVendors[$idx % count($warungVendors)];
            $menus[] = [
                'vendor_name' => $vendor,
                'menu_name' => $item['name'],
                'price' => rand($item['price'][0], $item['price'][1]),
                'description' => $item['desc'],
                'distance' => round(0.2 + (rand(0, 30) / 100), 2), // 0.2-0.5 km
                'portion_size' => round(6 + rand(0, 20) / 10, 1), // 6.0-8.0
                'service_speed' => round(6 + rand(0, 25) / 10, 1), // 6.0-8.5
                'taste_rating' => round(6 + rand(0, 20) / 10, 1), // 6.0-8.0
            ];
        }

        // Add 20 more warung items for variety
        $warungMenus2 = [
            ['name' => 'Nasi Goreng Seafood', 'price' => [13000, 17000], 'desc' => 'Nasi goreng dengan udang dan cumi'],
            ['name' => 'Soto Betawi', 'price' => [12000, 16000], 'desc' => 'Soto daging dengan santan dan tomat'],
            ['name' => 'Rawon', 'price' => [11000, 15000], 'desc' => 'Rawon daging dengan telur asin'],
            ['name' => 'Nasi Pecel Madiun', 'price' => [8000, 11000], 'desc' => 'Nasi pecel khas Madiun dengan rempeyek'],
            ['name' => 'Sop Ayam', 'price' => [9000, 13000], 'desc' => 'Sop ayam dengan wortel dan kentang'],
            ['name' => 'Nasi Goreng Jawa', 'price' => [10000, 14000], 'desc' => 'Nasi goreng bumbu jawa dengan pete'],
            ['name' => 'Pecel Ayam', 'price' => [11000, 15000], 'desc' => 'Ayam goreng dengan sayur pecel'],
            ['name' => 'Nasi Goreng Gila', 'price' => [12000, 16000], 'desc' => 'Nasi goreng dengan sosis, nugget, dan telur'],
            ['name' => 'Mie Godog Jawa', 'price' => [9000, 12000], 'desc' => 'Mie rebus kuah kental bumbu jawa'],
            ['name' => 'Nasi Bakar Ayam', 'price' => [12000, 16000], 'desc' => 'Nasi bakar dengan ayam suwir pedas'],
            ['name' => 'Tongseng Kambing', 'price' => [13000, 17000], 'desc' => 'Tongseng kambing dengan kuah gurih'],
            ['name' => 'Nasi Goreng Pete', 'price' => [11000, 15000], 'desc' => 'Nasi goreng dengan pete dan ikan teri'],
            ['name' => 'Oseng Mercon', 'price' => [10000, 14000], 'desc' => 'Oseng-oseng pedas dengan nasi putih'],
            ['name' => 'Nasi Goreng Tek-Tek', 'price' => [9000, 13000], 'desc' => 'Nasi goreng sederhana dengan telur'],
            ['name' => 'Mie Ayam Pangsit', 'price' => [10000, 13000], 'desc' => 'Mie ayam dengan pangsit goreng dan rebus'],
            ['name' => 'Nasi Krawu', 'price' => [11000, 15000], 'desc' => 'Nasi dengan daging sapi dan serundeng'],
            ['name' => 'Sate Kambing', 'price' => [13000, 17000], 'desc' => 'Sate kambing muda dengan bumbu kacang'],
            ['name' => 'Nasi Goreng Teri', 'price' => [9000, 12000], 'desc' => 'Nasi goreng dengan ikan teri medan'],
            ['name' => 'Bakso Beranak', 'price' => [11000, 14000], 'desc' => 'Bakso besar berisi telur puyuh'],
            ['name' => 'Lontong Kikil', 'price' => [10000, 13000], 'desc' => 'Lontong dengan kikil sapi dan kuah santan'],
        ];

        foreach ($warungMenus2 as $idx => $item) {
            $vendor = $warungVendors[$idx % count($warungVendors)];
            $menus[] = [
                'vendor_name' => $vendor,
                'menu_name' => $item['name'],
                'price' => rand($item['price'][0], $item['price'][1]),
                'description' => $item['desc'],
                'distance' => round(0.2 + (rand(0, 30) / 100), 2),
                'portion_size' => round(6 + rand(0, 20) / 10, 1),
                'service_speed' => round(6 + rand(0, 25) / 10, 1),
                'taste_rating' => round(6 + rand(0, 20) / 10, 1),
            ];
        }

        // Category 2: Kantin Kampus (50 items) - very close, moderate price
        $kantinVendors = ['Kantin Kampus Teknik', 'Kantin Kampus Ekonomi', 'Kantin Kampus FIK', 
                          'Kantin Kampus Pusat', 'Kantin Kampus Hukum', 'Kantin Kampus Kedokteran',
                          'Kantin Kampus Pertanian', 'Kantin Kampus MIPA', 'Kantin Kampus FBS',
                          'Kantin Kampus Pascasarjana'];
        
        $kantinMenus = [
            ['name' => 'Nasi Goreng Seafood', 'price' => [15000, 20000], 'desc' => 'Nasi goreng dengan udang, cumi, dan telur'],
            ['name' => 'Gado-Gado Jakarta', 'price' => [12000, 16000], 'desc' => 'Sayuran dengan saus kacang, lontong, dan telur rebus'],
            ['name' => 'Nasi Ayam Geprek', 'price' => [14000, 18000], 'desc' => 'Nasi dengan ayam crispy dan sambal geprek pedas'],
            ['name' => 'Sate Ayam Komplit', 'price' => [15000, 19000], 'desc' => 'Sate ayam dengan bumbu kacang, lontong, dan lalapan'],
            ['name' => 'Bakso Malang', 'price' => [13000, 17000], 'desc' => 'Bakso dengan mie, tahu, siomay, dan kuah kaldu'],
            ['name' => 'Nasi Goreng Spesial', 'price' => [16000, 20000], 'desc' => 'Nasi goreng dengan ayam, udang, dan telur'],
            ['name' => 'Mie Goreng Seafood', 'price' => [14000, 18000], 'desc' => 'Mie goreng dengan seafood lengkap'],
            ['name' => 'Ayam Bakar Madu', 'price' => [16000, 21000], 'desc' => 'Ayam bakar dengan saus madu dan lalapan'],
            ['name' => 'Nasi Ayam Teriyaki', 'price' => [15000, 19000], 'desc' => 'Nasi dengan ayam teriyaki dan sayuran'],
            ['name' => 'Bakso Aci Mercon', 'price' => [12000, 16000], 'desc' => 'Bakso aci pedas dengan kuah kaldu'],
            ['name' => 'Nasi Goreng Kambing', 'price' => [17000, 22000], 'desc' => 'Nasi goreng dengan daging kambing muda'],
            ['name' => 'Capcay Seafood', 'price' => [14000, 18000], 'desc' => 'Capcay dengan seafood dan kuah kental'],
            ['name' => 'Nasi Ayam Katsu', 'price' => [16000, 21000], 'desc' => 'Nasi dengan ayam katsu dan saus'],
            ['name' => 'Mie Ayam Ceker', 'price' => [13000, 17000], 'desc' => 'Mie ayam dengan ceker empuk'],
            ['name' => 'Nasi Goreng Jawa Komplit', 'price' => [15000, 19000], 'desc' => 'Nasi goreng jawa dengan lauk lengkap'],
            ['name' => 'Soto Lamongan', 'price' => [13000, 17000], 'desc' => 'Soto ayam khas Lamongan dengan koya'],
            ['name' => 'Nasi Bebek Goreng', 'price' => [18000, 23000], 'desc' => 'Nasi dengan bebek goreng dan sambal'],
            ['name' => 'Nasi Goreng Korea', 'price' => [16000, 20000], 'desc' => 'Nasi goreng kimchi dengan bulgogi'],
            ['name' => 'Kwetiau Goreng Seafood', 'price' => [15000, 19000], 'desc' => 'Kwetiau goreng dengan seafood segar'],
            ['name' => 'Nasi Ayam Crispy', 'price' => [15000, 19000], 'desc' => 'Nasi dengan ayam crispy dan saus'],
            ['name' => 'Sop Buntut', 'price' => [19000, 24000], 'desc' => 'Sop buntut sapi dengan wortel dan kentang'],
            ['name' => 'Nasi Goreng Rendang', 'price' => [17000, 21000], 'desc' => 'Nasi goreng dengan rendang sapi'],
            ['name' => 'Mie Goreng Jawa Spesial', 'price' => [14000, 18000], 'desc' => 'Mie goreng jawa dengan telur dan ayam'],
            ['name' => 'Nasi Iga Bakar', 'price' => [20000, 25000], 'desc' => 'Nasi dengan iga bakar bumbu kecap'],
            ['name' => 'Bakso Tetelan', 'price' => [14000, 18000], 'desc' => 'Bakso dengan tetelan sapi empuk'],
            ['name' => 'Nasi Goreng Mentega', 'price' => [15000, 19000], 'desc' => 'Nasi goreng dengan mentega dan udang'],
            ['name' => 'Ayam Geprek Mozarella', 'price' => [17000, 22000], 'desc' => 'Ayam geprek dengan keju mozarella leleh'],
            ['name' => 'Nasi Goreng Tom Yam', 'price' => [16000, 20000], 'desc' => 'Nasi goreng bumbu tom yam seafood'],
            ['name' => 'Mie Goreng Tek-Tek Spesial', 'price' => [13000, 17000], 'desc' => 'Mie goreng dengan telur dan sayuran'],
            ['name' => 'Nasi Ayam Blackpepper', 'price' => [15000, 19000], 'desc' => 'Nasi dengan ayam saus lada hitam'],
            ['name' => 'Sate Taichan', 'price' => [14000, 18000], 'desc' => 'Sate ayam taichan pedas tanpa bumbu kacang'],
            ['name' => 'Nasi Goreng Magelangan', 'price' => [14000, 18000], 'desc' => 'Nasi goreng dengan mie dan telur'],
            ['name' => 'Ayam Bakar Bumbu Rujak', 'price' => [16000, 20000], 'desc' => 'Ayam bakar dengan bumbu rujak pedas manis'],
            ['name' => 'Nasi Goreng Cumi Hitam', 'price' => [17000, 22000], 'desc' => 'Nasi goreng dengan tinta cumi'],
            ['name' => 'Bakso Keju', 'price' => [15000, 19000], 'desc' => 'Bakso berisi keju dengan kuah kaldu'],
            ['name' => 'Nasi Ayam Kung Pao', 'price' => [16000, 20000], 'desc' => 'Nasi dengan ayam kung pao dan kacang'],
            ['name' => 'Mie Ayam Abang-Abang', 'price' => [13000, 17000], 'desc' => 'Mie ayam level pedas dengan bakso'],
            ['name' => 'Nasi Goreng Gila Pedas', 'price' => [15000, 19000], 'desc' => 'Nasi goreng super pedas dengan lauk lengkap'],
            ['name' => 'Ayam Penyet Sambal Ijo', 'price' => [15000, 19000], 'desc' => 'Ayam penyet dengan sambal hijau pedas'],
            ['name' => 'Nasi Goreng Kampung Spesial', 'price' => [14000, 18000], 'desc' => 'Nasi goreng kampung dengan ikan asin'],
            ['name' => 'Bakso Rudal', 'price' => [16000, 20000], 'desc' => 'Bakso jumbo super pedas level rudal'],
            ['name' => 'Nasi Ayam Sambal Matah', 'price' => [15000, 19000], 'desc' => 'Nasi dengan ayam dan sambal matah khas Bali'],
            ['name' => 'Mie Goreng Aceh', 'price' => [15000, 19000], 'desc' => 'Mie goreng bumbu aceh pedas'],
            ['name' => 'Nasi Goreng Babat', 'price' => [14000, 18000], 'desc' => 'Nasi goreng dengan babat sapi'],
            ['name' => 'Kwetiau Siram Seafood', 'price' => [16000, 20000], 'desc' => 'Kwetiau dengan kuah seafood kental'],
            ['name' => 'Nasi Bebek Bakar Pedas', 'price' => [19000, 24000], 'desc' => 'Nasi dengan bebek bakar sambal pedas'],
            ['name' => 'Bakso Jumbo Komplit', 'price' => [15000, 19000], 'desc' => 'Bakso jumbo dengan berbagai isian'],
            ['name' => 'Nasi Goreng Nanas', 'price' => [16000, 20000], 'desc' => 'Nasi goreng dengan potongan nanas'],
            ['name' => 'Ayam Geprek Keju', 'price' => [16000, 21000], 'desc' => 'Ayam geprek dengan topping keju parut'],
            ['name' => 'Nasi Capcay Kuah', 'price' => [14000, 18000], 'desc' => 'Nasi dengan capcay kuah sayuran lengkap'],
        ];

        foreach ($kantinMenus as $idx => $item) {
            $vendor = $kantinVendors[$idx % count($kantinVendors)];
            $menus[] = [
                'vendor_name' => $vendor,
                'menu_name' => $item['name'],
                'price' => rand($item['price'][0], $item['price'][1]),
                'description' => $item['desc'],
                'distance' => round(0.05 + (rand(0, 15) / 100), 2), // 0.05-0.2 km
                'portion_size' => round(7 + rand(0, 20) / 10, 1), // 7.0-9.0
                'service_speed' => round(7 + rand(0, 25) / 10, 1), // 7.0-9.5
                'taste_rating' => round(7 + rand(0, 20) / 10, 1), // 7.0-9.0
            ];
        }

        // Category 3: Restoran (60 items) - higher quality, further distance, higher price
        $restoranVendors = ['Restoran Padang Sederhana', 'Restoran Sunda Rasa', 'Restoran Ayam Bakar', 
                            'Restoran Seafood Bahari', 'Restoran Jawa Timur', 'Restoran Betawi',
                            'Restoran Boga Rasa', 'Restoran Nusantara', 'Restoran Rempah Nusantara',
                            'Restoran Bumbu Desa', 'Restoran Selera Nusantara', 'Restoran Pujasera'];
        
        $restoranMenus = [
            ['name' => 'Nasi Padang Rendang', 'price' => [23000, 30000], 'desc' => 'Nasi dengan rendang sapi, sayur gulai, dan sambal hijau'],
            ['name' => 'Nasi Timbel Komplit', 'price' => [25000, 32000], 'desc' => 'Nasi timbel dengan ayam goreng, tahu, tempe, dan lalapan'],
            ['name' => 'Ayam Bakar Taliwang', 'price' => [27000, 35000], 'desc' => 'Ayam bakar bumbu taliwang dengan plecing kangkung'],
            ['name' => 'Nasi Goreng Seafood Spesial', 'price' => [24000, 31000], 'desc' => 'Nasi goreng dengan udang, cumi, ikan, dan kerang'],
            ['name' => 'Rawon Daging Spesial', 'price' => [23000, 30000], 'desc' => 'Rawon daging sapi dengan telur asin dan sambal'],
            ['name' => 'Soto Betawi Komplit', 'price' => [22000, 28000], 'desc' => 'Soto daging sapi dengan santan, tomat, dan emping'],
            ['name' => 'Nasi Liwet Solo Komplit', 'price' => [20000, 26000], 'desc' => 'Nasi liwet dengan ayam suwir, telur, dan sambal goreng'],
            ['name' => 'Nasi Uduk Komplit', 'price' => [18000, 24000], 'desc' => 'Nasi uduk dengan ayam goreng, telur balado, dan sambal'],
            ['name' => 'Iga Bakar Madu', 'price' => [32000, 40000], 'desc' => 'Iga sapi bakar dengan saus madu spesial'],
            ['name' => 'Gurame Bakar Kecap', 'price' => [35000, 45000], 'desc' => 'Ikan gurame bakar dengan saus kecap manis'],
            ['name' => 'Bebek Goreng Kremes', 'price' => [28000, 36000], 'desc' => 'Bebek goreng dengan kremesan dan sambal'],
            ['name' => 'Nasi Padang Gulai Ikan', 'price' => [24000, 31000], 'desc' => 'Nasi dengan gulai ikan kakap dan sambal'],
            ['name' => 'Sop Konro Makassar', 'price' => [26000, 33000], 'desc' => 'Sop iga sapi bumbu konro khas Makassar'],
            ['name' => 'Nasi Padang Ayam Pop', 'price' => [22000, 28000], 'desc' => 'Nasi dengan ayam pop khas Padang'],
            ['name' => 'Bebek Bakar Sambal Ijo', 'price' => [29000, 37000], 'desc' => 'Bebek bakar dengan sambal hijau pedas'],
            ['name' => 'Ikan Bakar Bumbu Kecap', 'price' => [28000, 36000], 'desc' => 'Ikan bakar dengan bumbu kecap manis'],
            ['name' => 'Nasi Padang Dendeng Balado', 'price' => [27000, 34000], 'desc' => 'Nasi dengan dendeng balado pedas'],
            ['name' => 'Sate Maranggi Purwakarta', 'price' => [25000, 32000], 'desc' => 'Sate sapi khas Purwakarta dengan bumbu spesial'],
            ['name' => 'Nasi Timbel Ayam Bakar', 'price' => [24000, 30000], 'desc' => 'Nasi timbel dengan ayam bakar dan lalapan'],
            ['name' => 'Gulai Kambing Spesial', 'price' => [30000, 38000], 'desc' => 'Gulai kambing dengan kuah santan kental'],
            ['name' => 'Nasi Padang Paru Goreng', 'price' => [21000, 27000], 'desc' => 'Nasi dengan paru goreng balado'],
            ['name' => 'Ayam Goreng Kalasan', 'price' => [23000, 29000], 'desc' => 'Ayam goreng bumbu kalasan dengan sambal'],
            ['name' => 'Pepes Ikan Mas', 'price' => [26000, 33000], 'desc' => 'Pepes ikan mas dengan bumbu rempah'],
            ['name' => 'Nasi Padang Telur Dadar', 'price' => [19000, 24000], 'desc' => 'Nasi dengan telur dadar padang dan sambal'],
            ['name' => 'Ikan Gurame Asam Manis', 'price' => [34000, 42000], 'desc' => 'Gurame goreng dengan saus asam manis'],
            ['name' => 'Bebek Goreng Sinjay', 'price' => [28000, 35000], 'desc' => 'Bebek goreng empuk khas Sinjay'],
            ['name' => 'Nasi Padang Udang Balado', 'price' => [28000, 35000], 'desc' => 'Nasi dengan udang balado pedas'],
            ['name' => 'Ayam Betutu Bali', 'price' => [30000, 38000], 'desc' => 'Ayam betutu bumbu khas Bali'],
            ['name' => 'Nasi Campur Bali', 'price' => [25000, 32000], 'desc' => 'Nasi campur dengan lauk khas Bali'],
            ['name' => 'Iga Penyet Sambal Mercon', 'price' => [31000, 39000], 'desc' => 'Iga penyet dengan sambal super pedas'],
            ['name' => 'Sop Iga Sapi', 'price' => [27000, 34000], 'desc' => 'Sop iga sapi dengan sayuran segar'],
            ['name' => 'Nasi Padang Kikil Balado', 'price' => [22000, 28000], 'desc' => 'Nasi dengan kikil sapi balado'],
            ['name' => 'Ayam Goreng Lengkuas', 'price' => [24000, 30000], 'desc' => 'Ayam goreng bumbu lengkuas harum'],
            ['name' => 'Pepes Ayam Kemangi', 'price' => [25000, 31000], 'desc' => 'Pepes ayam dengan kemangi segar'],
            ['name' => 'Nasi Timbel Ikan Asin', 'price' => [21000, 27000], 'desc' => 'Nasi timbel dengan ikan asin dan lalapan'],
            ['name' => 'Gulai Kepala Ikan', 'price' => [29000, 36000], 'desc' => 'Gulai kepala ikan kakap khas Padang'],
            ['name' => 'Bebek Madura Pedas', 'price' => [28000, 35000], 'desc' => 'Bebek goreng bumbu Madura pedas'],
            ['name' => 'Nasi Padang Tunjang', 'price' => [23000, 29000], 'desc' => 'Nasi dengan tunjang sapi empuk'],
            ['name' => 'Ayam Bakar Bumbu Bali', 'price' => [26000, 33000], 'desc' => 'Ayam bakar dengan bumbu Bali pedas'],
            ['name' => 'Ikan Nila Bakar', 'price' => [24000, 30000], 'desc' => 'Ikan nila bakar dengan sambal kecap'],
            ['name' => 'Nasi Padang Soto Padang', 'price' => [21000, 27000], 'desc' => 'Nasi dengan soto Padang berkuah'],
            ['name' => 'Empal Gentong Cirebon', 'price' => [25000, 32000], 'desc' => 'Empal daging dengan kuah santan khas Cirebon'],
            ['name' => 'Ayam Goreng Bumbu Kuning', 'price' => [23000, 29000], 'desc' => 'Ayam goreng bumbu kuning rempah'],
            ['name' => 'Ikan Patin Bakar', 'price' => [27000, 34000], 'desc' => 'Ikan patin bakar dengan sambal dabu-dabu'],
            ['name' => 'Nasi Padang Cumi Hitam', 'price' => [26000, 33000], 'desc' => 'Nasi dengan cumi hitam pedas'],
            ['name' => 'Bebek Goreng Surabaya', 'price' => [27000, 34000], 'desc' => 'Bebek goreng khas Surabaya dengan sambal'],
            ['name' => 'Ayam Kremes Bu Tini', 'price' => [24000, 30000], 'desc' => 'Ayam goreng dengan kremesan renyah'],
            ['name' => 'Nasi Timbel Sunda Lengkap', 'price' => [26000, 33000], 'desc' => 'Nasi timbel dengan lauk sunda lengkap'],
            ['name' => 'Gulai Otak', 'price' => [28000, 35000], 'desc' => 'Gulai otak sapi khas Padang'],
            ['name' => 'Ikan Bakar Manokwari', 'price' => [29000, 36000], 'desc' => 'Ikan bakar bumbu khas Manokwari'],
            ['name' => 'Nasi Padang Kerang Balado', 'price' => [25000, 32000], 'desc' => 'Nasi dengan kerang hijau balado'],
            ['name' => 'Ayam Goreng Ungkep', 'price' => [22000, 28000], 'desc' => 'Ayam goreng diungkep bumbu rempah'],
            ['name' => 'Bebek Bakar Madu', 'price' => [30000, 38000], 'desc' => 'Bebek bakar dengan saus madu manis'],
            ['name' => 'Nasi Padang Cumi Balado', 'price' => [27000, 34000], 'desc' => 'Nasi dengan cumi balado pedas'],
            ['name' => 'Iga Bakar BBQ', 'price' => [33000, 41000], 'desc' => 'Iga sapi bakar dengan saus BBQ'],
            ['name' => 'Pepes Tahu Teri', 'price' => [20000, 26000], 'desc' => 'Pepes tahu dengan ikan teri medan'],
            ['name' => 'Nasi Timbel Empal Gepuk', 'price' => [27000, 34000], 'desc' => 'Nasi timbel dengan empal gepuk empuk'],
            ['name' => 'Ayam Taliwang Lombok', 'price' => [29000, 36000], 'desc' => 'Ayam taliwang pedas khas Lombok'],
            ['name' => 'Gurame Goreng Crispy', 'price' => [36000, 45000], 'desc' => 'Gurame goreng crispy dengan saus'],
            ['name' => 'Nasi Padang Usus Balado', 'price' => [23000, 29000], 'desc' => 'Nasi dengan usus sapi balado pedas'],
        ];

        foreach ($restoranMenus as $idx => $item) {
            $vendor = $restoranVendors[$idx % count($restoranVendors)];
            $menus[] = [
                'vendor_name' => $vendor,
                'menu_name' => $item['name'],
                'price' => rand($item['price'][0], $item['price'][1]),
                'description' => $item['desc'],
                'distance' => round(0.8 + (rand(0, 120) / 100), 2), // 0.8-2.0 km
                'portion_size' => round(8 + rand(0, 20) / 10, 1), // 8.0-10.0
                'service_speed' => round(6 + rand(0, 20) / 10, 1), // 6.0-8.0 (slower due to quality)
                'taste_rating' => round(8 + rand(0, 20) / 10, 1), // 8.0-10.0
            ];
        }

        return $menus;
    }

    /**
     * Create comprehensive evaluation values for all 8 criteria.
     * 
     * @param Menu $menu
     * @param \Illuminate\Support\Collection $criteria
     * @param float $price
     * @param float $distance
     * @param float $portionSize
     * @param float $serviceSpeed
     * @param float $tasteRating
     */
    private function createEvaluations(
        Menu $menu, 
        $criteria, 
        float $price, 
        float $distance, 
        float $portionSize, 
        float $serviceSpeed, 
        float $tasteRating
    ): void {
        // C1: Kandungan Gizi (Nutritional Content) - benefit (scale: 1-10)
        $nutritionScore = $this->calculateNutritionScore($price, $menu->menu_name);
        MenuEvaluation::create([
            'menu_id' => $menu->id,
            'criterion_id' => $criteria['C1']->id,
            'value' => $nutritionScore,
        ]);

        // C2: Jarak ke Kampus (Distance to Campus) - cost (actual distance in km)
        MenuEvaluation::create([
            'menu_id' => $menu->id,
            'criterion_id' => $criteria['C2']->id,
            'value' => $distance,
        ]);

        // C3: Higienitas (Hygiene) - benefit (scale: 1-10)
        $hygieneScore = $this->calculateHygieneScore($price, $menu->vendor_name);
        MenuEvaluation::create([
            'menu_id' => $menu->id,
            'criterion_id' => $criteria['C3']->id,
            'value' => $hygieneScore,
        ]);

        // C4: Variasi Menu (Menu Variety) - benefit (scale: 1-10)
        $varietyScore = $this->calculateVarietyScore($menu->menu_name, $menu->description);
        MenuEvaluation::create([
            'menu_id' => $menu->id,
            'criterion_id' => $criteria['C4']->id,
            'value' => $varietyScore,
        ]);

        // C5: Harga (Price) - cost (actual price in Rupiah)
        MenuEvaluation::create([
            'menu_id' => $menu->id,
            'criterion_id' => $criteria['C5']->id,
            'value' => $price,
        ]);

        // C6: Porsi (Portion Size) - benefit (scale: 1-10)
        MenuEvaluation::create([
            'menu_id' => $menu->id,
            'criterion_id' => $criteria['C6']->id,
            'value' => $portionSize,
        ]);

        // C7: Kecepatan Layanan (Service Speed) - benefit (scale: 1-10)
        MenuEvaluation::create([
            'menu_id' => $menu->id,
            'criterion_id' => $criteria['C7']->id,
            'value' => $serviceSpeed,
        ]);

        // C8: Rasa (Taste) - benefit (scale: 1-10)
        MenuEvaluation::create([
            'menu_id' => $menu->id,
            'criterion_id' => $criteria['C8']->id,
            'value' => $tasteRating,
        ]);
    }

    /**
     * Calculate nutrition score based on price and menu type.
     * Higher price generally correlates with better ingredients.
     * 
     * @param float $price
     * @param string $menuName
     * @return float
     */
    private function calculateNutritionScore(float $price, string $menuName): float
    {
        // Base score from price (7k-45k range mapped to 3-9 scale)
        $baseScore = 3 + (($price - 7000) / 38000) * 6;

        // Bonus for protein-rich items
        $proteinBonus = 0;
        if (stripos($menuName, 'seafood') !== false || stripos($menuName, 'rendang') !== false || 
            stripos($menuName, 'iga') !== false || stripos($menuName, 'gurame') !== false) {
            $proteinBonus = 1.5;
        } elseif (stripos($menuName, 'ayam') !== false || stripos($menuName, 'daging') !== false ||
                  stripos($menuName, 'bebek') !== false || stripos($menuName, 'ikan') !== false) {
            $proteinBonus = 1.0;
        } elseif (stripos($menuName, 'telur') !== false || stripos($menuName, 'bakso') !== false) {
            $proteinBonus = 0.5;
        }

        // Bonus for vegetable-rich items
        $vegBonus = 0;
        if (stripos($menuName, 'gado') !== false || stripos($menuName, 'pecel') !== false || 
            stripos($menuName, 'sayur') !== false || stripos($menuName, 'capcay') !== false ||
            stripos($menuName, 'lalapan') !== false) {
            $vegBonus = 0.8;
        }

        $score = $baseScore + $proteinBonus + $vegBonus;
        
        // Cap between 1 and 10
        return max(1, min(10, round($score, 1)));
    }

    /**
     * Calculate hygiene score based on price and vendor type.
     * Restaurants generally have better hygiene than street food.
     * 
     * @param float $price
     * @param string $vendorName
     * @return float
     */
    private function calculateHygieneScore(float $price, string $vendorName): float
    {
        // Base score from vendor type
        $baseScore = 5.0;
        
        if (stripos($vendorName, 'restoran') !== false) {
            // Restaurants: 7.5-9.5 range
            $baseScore = 7.5 + (($price - 18000) / 27000) * 2;
        } elseif (stripos($vendorName, 'kantin') !== false) {
            // Campus cafeteria: 6.5-8.5 range
            $baseScore = 6.5 + (($price - 12000) / 13000) * 2;
        } else {
            // Warung (street food): 4.5-7.5 range
            $baseScore = 4.5 + (($price - 7000) / 10000) * 3;
        }

        // Add small variation
        $variation = (rand(0, 10) - 5) * 0.1;
        $score = $baseScore + $variation;
        
        // Clamp between 1 and 10
        return max(1, min(10, round($score, 1)));
    }

    /**
     * Calculate variety score based on menu complexity.
     * More ingredients and complex dishes score higher.
     * 
     * @param string $menuName
     * @param string $description
     * @return float
     */
    private function calculateVarietyScore(string $menuName, string $description): float
    {
        // Count ingredients mentioned in description
        $ingredients = ['ayam', 'telur', 'sayur', 'sambal', 'kerupuk', 'udang', 'cumi', 
                       'ikan', 'daging', 'tahu', 'tempe', 'bakso', 'mie', 'lontong', 
                       'nasi', 'santan', 'bumbu', 'lalapan', 'keju', 'sosis', 'nugget',
                       'wortel', 'kentang', 'jagung', 'kacang', 'pete', 'teri'];
        
        $count = 0;
        $descriptionLower = strtolower($description);
        foreach ($ingredients as $ingredient) {
            if (stripos($descriptionLower, $ingredient) !== false) {
                $count++;
            }
        }

        // Base score: 4-9 based on ingredient count
        $baseScore = min(9, 4 + ($count * 0.7));

        // Bonus for "komplit", "spesial", or "lengkap" in name or description
        $bonus = 0;
        $fullText = strtolower($menuName . ' ' . $description);
        if (stripos($fullText, 'komplit') !== false || stripos($fullText, 'spesial') !== false ||
            stripos($fullText, 'lengkap') !== false) {
            $bonus = 1.0;
        } elseif (stripos($menuName, 'campur') !== false) {
            $bonus = 0.8;
        }

        $score = $baseScore + $bonus;
        
        // Clamp between 1 and 10
        return max(1, min(10, round($score, 1)));
    }
}
