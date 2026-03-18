<?php

namespace Database\Seeders;

use App\Enums\EventStatus;
use App\Enums\OrderStatus;
use App\Enums\TicketStatus;
use App\Models\Checkin;
use App\Models\Event;
use App\Models\EventCollaborator;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Organizer;
use App\Models\Participant;
use App\Models\Ticket;
use App\Models\TicketType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    private array $names = [
        'Ana Paula Souza', 'Carlos Eduardo Mendes', 'Fernanda Lima', 'Ricardo Pereira',
        'Mariana Costa', 'João Victor Santos', 'Beatriz Oliveira', 'Lucas Ferreira',
        'Camila Rodrigues', 'Pedro Henrique Alves', 'Juliana Martins', 'Rafael Gomes',
        'Isabela Carvalho', 'Thiago Nascimento', 'Larissa Ribeiro', 'Felipe Barbosa',
        'Natalia Freitas', 'Gustavo Lima', 'Aline Cavalcante', 'Diego Monteiro',
        'Priscila Azevedo', 'Rodrigo Teixeira', 'Vanessa Correia', 'André Moreira',
        'Tatiane Melo', 'Leandro Cardoso', 'Renata Pinto', 'Bruno Andrade',
        'Simone Vieira', 'Mateus Cunha', 'Débora Ramos', 'Vinícius Campos',
        'Patrícia Nunes', 'Henrique Lopes', 'Alessandra Braga', 'Gabriel Mendes',
        'Fabiana Sousa', 'Eduardo Fonseca', 'Karina Duarte', 'Marcelo Araújo',
        'Sandra Borges', 'Paulo Henrique Luz', 'Cristina Veiga', 'Alexandre Rocha',
        'Monica Batista', 'Robson Farias', 'Elaine Queiroz', 'Sérgio Macedo',
        'Adriana Peixoto', 'Leonardo Tavares',
    ];

    private int $nameIdx = 0;

    public function run(): void
    {
        // ─── Usuário principal ────────────────────────────────────────────────
        $mainUser = User::create([
            'name'              => 'João Filipi',
            'email'             => 'joao@taketicket.com.br',
            'password'          => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        $mainOrganizer = Organizer::create([
            'user_id'     => $mainUser->id,
            'name'        => 'TakeTicket Eventos',
            'slug'        => 'taketicket-eventos',
            'description' => 'Organizadora líder em eventos culturais, esportivos e corporativos no Brasil.',
            'document'    => '12.345.678/0001-99',
            'phone'       => '(11) 98765-4321',
            'address'     => 'Av. Paulista, 1000',
            'city'        => 'São Paulo',
            'state'       => 'SP',
            'postal_code' => '01310-100',
        ]);

        // ─── Segundo organizador ──────────────────────────────────────────────
        $rioUser = User::create([
            'name'              => 'Bianca Rocha',
            'email'             => 'bianca@rioeventos.com.br',
            'password'          => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        $rioOrganizer = Organizer::create([
            'user_id'     => $rioUser->id,
            'name'        => 'Rio Eventos',
            'slug'        => 'rio-eventos',
            'description' => 'Eventos culturais no Rio de Janeiro.',
            'document'    => '98.765.432/0001-11',
            'phone'       => '(21) 97654-3210',
            'address'     => 'Av. Atlântica, 1702',
            'city'        => 'Rio de Janeiro',
            'state'       => 'RJ',
            'postal_code' => '22021-001',
        ]);

        // ─── 30 compradores ───────────────────────────────────────────────────
        $buyers = collect($this->names)->map(fn ($name, $i) => User::create([
            'name'              => $name,
            'email'             => Str::slug($name) . '@email.com',
            'password'          => Hash::make('password'),
            'email_verified_at' => now(),
        ]));

        // ─── Usuários colaboradores (sem organizer) ───────────────────────────
        $staffUsers = collect([
            ['name' => 'Marcos Staff',   'email' => 'marcos.staff@email.com'],
            ['name' => 'Julia Staff',    'email' => 'julia.staff@email.com'],
            ['name' => 'Pedro Staff',    'email' => 'pedro.staff@email.com'],
            ['name' => 'Carla Staff',    'email' => 'carla.staff@email.com'],
            ['name' => 'Tiago Staff',    'email' => 'tiago.staff@email.com'],
        ])->map(fn ($d) => User::create([
            ...$d,
            'password'          => Hash::make('password'),
            'email_verified_at' => now(),
        ]));

        // =====================================================================
        // EVENTOS PRINCIPAIS (detalhados)
        // =====================================================================

        // ─── Evento 1: Corrida da Noite (upcoming, com colaboradores) ─────────
        $event1 = $this->makeEvent($mainOrganizer, [
            'title'      => 'Corrida da Noite SP 2026',
            'slug'       => 'corrida-da-noite-sp-2026',
            'description'=> "A maior corrida noturna de São Paulo está de volta! Percursos de 5km e 10km pelo coração da cidade, com iluminação especial e DJ ao vivo na largada.\n\nInclui: chip de cronometragem, camiseta oficial, medalha de finisher e kit hidratação.",
            'location'   => 'Parque Ibirapuera',
            'address'    => 'Av. Pedro Álvares Cabral, s/n',
            'city'       => 'São Paulo', 'state' => 'SP',
            'start_date' => now()->addDays(25)->setTime(20, 0),
            'end_date'   => now()->addDays(25)->setTime(23, 30),
            'status'     => EventStatus::PUBLISHED,
        ]);

        $tt1a = $this->makeTicketType($event1, 'Corrida 5km', 89.90, 500, 360);
        $tt1b = $this->makeTicketType($event1, 'Corrida 10km', 119.90, 300, 220);
        $tt1c = $this->makeTicketType($event1, 'VIP + Kit Premium', 199.90, 50, 30);

        foreach (range(0, 14) as $i) {
            $tt = [$tt1a, $tt1b, $tt1c][$i % 3];
            $this->createPaidOrder($buyers[$i % count($buyers)], $event1, $tt, rand(1, 2));
        }

        // Colaboradores do evento 1
        $this->addCollaborator($event1, $mainUser, $staffUsers[0], 'active');
        $this->addCollaborator($event1, $mainUser, $staffUsers[1], 'active');
        $this->addCollaborator($event1, $mainUser, null, 'pending', 'novo.staff1@email.com');

        // ─── Evento 2: Festival Indie Sampa (upcoming, grande) ───────────────
        $event2 = $this->makeEvent($mainOrganizer, [
            'title'      => 'Festival Indie Sampa 2026',
            'slug'       => 'festival-indie-sampa-2026',
            'description'=> "Dois dias de muito rock, indie e pop alternativo no centro de SP. Mais de 15 bandas nacionais e internacionais em 3 palcos simultâneos.",
            'location'   => 'Espaço das Américas',
            'address'    => 'Rua Tagipuru, 795',
            'city'       => 'São Paulo', 'state' => 'SP',
            'start_date' => now()->addDays(8)->setTime(18, 0),
            'end_date'   => now()->addDays(9)->setTime(2, 0),
            'status'     => EventStatus::PUBLISHED,
        ]);

        $tt2a = $this->makeTicketType($event2, 'Pista — 1 dia', 180.00, 1000, 634);
        $tt2b = $this->makeTicketType($event2, 'Pista — 2 dias', 300.00, 500, 312);
        $tt2c = $this->makeTicketType($event2, 'VIP — 2 dias', 650.00, 100, 78);

        foreach (range(0, 14) as $i) {
            $tt = [$tt2a, $tt2b, $tt2c][$i % 3];
            $this->createPaidOrder($buyers[$i % count($buyers)], $event2, $tt, rand(1, 3));
        }

        $this->addCollaborator($event2, $mainUser, $staffUsers[2], 'active');
        $this->addCollaborator($event2, $mainUser, $staffUsers[3], 'active');
        $this->addCollaborator($event2, $mainUser, $staffUsers[4], 'active');
        $this->addCollaborator($event2, $mainUser, null, 'pending', 'novo.staff2@email.com');

        // ─── Evento 3: TechConf (passado, com check-ins feitos) ──────────────
        $event3 = $this->makeEvent($mainOrganizer, [
            'title'      => 'TechConf Brasil 2026',
            'slug'       => 'techconf-brasil-2026',
            'description'=> "A maior conferência de tecnologia do Brasil. Dois dias de palestras, workshops e networking sobre IA, Cloud, Mobile e DevOps.",
            'location'   => 'Transamerica Expo Center',
            'address'    => 'Av. Dr. Mário Vilas Boas Rodrigues, 387',
            'city'       => 'São Paulo', 'state' => 'SP',
            'start_date' => now()->subDays(2)->setTime(9, 0),
            'end_date'   => now()->subDays(1)->setTime(18, 0),
            'status'     => EventStatus::PUBLISHED,
        ]);

        $tt3a = $this->makeTicketType($event3, 'Ingresso Geral — 2 dias', 490.00, 800, 0, past: true);
        $tt3b = $this->makeTicketType($event3, 'VIP — 2 dias', 990.00, 100, 5, past: true);

        $checkinOrders = [];
        foreach (range(0, 9) as $i) {
            $tt = $i < 7 ? $tt3a : $tt3b;
            $checkinOrders[] = $this->createPaidOrder($buyers[$i % count($buyers)], $event3, $tt, 1);
        }

        // Marcar todos como check-in feito
        foreach ($checkinOrders as $order) {
            $tickets = Ticket::whereIn('order_item_id', $order->items->pluck('id'))->get();
            foreach ($tickets as $ticket) {
                $at = $event3->start_date->copy()->addMinutes(rand(5, 120));
                $ticket->update(['status' => TicketStatus::USED, 'checked_in_at' => $at]);
                Checkin::create(['ticket_id' => $ticket->id, 'checked_by' => $mainUser->id, 'checked_at' => $at]);
            }
        }

        // ─── Evento 4: Workshop de Fotografia (gratuito, próximo) ─────────────
        $event4 = $this->makeEvent($mainOrganizer, [
            'title'      => 'Workshop de Fotografia Urbana',
            'slug'       => 'workshop-fotografia-urbana-2026',
            'description'=> "Aprenda técnicas de fotografia urbana com profissionais renomados. Traga seu celular ou câmera. Vagas limitadas.",
            'location'   => 'Museu da Luz',
            'address'    => 'Praça da Luz, s/n',
            'city'       => 'São Paulo', 'state' => 'SP',
            'start_date' => now()->addDays(3)->setTime(9, 0),
            'end_date'   => now()->addDays(3)->setTime(17, 0),
            'status'     => EventStatus::PUBLISHED,
        ]);

        $tt4a = $this->makeTicketType($event4, 'Inscrição Gratuita', 0, 30, 12);

        foreach (range(0, 4) as $i) {
            $this->createPaidOrder($buyers[$i], $event4, $tt4a, 1, 0);
        }

        // ─── Evento 5: Rock in Rio SP (grande, upcoming) ──────────────────────
        $event5 = $this->makeEvent($mainOrganizer, [
            'title'      => 'Rock in Rio São Paulo 2026',
            'slug'       => 'rock-in-rio-sp-2026',
            'description'=> "A edição paulistana do maior festival de música do mundo. 4 dias, 6 palcos, mais de 50 atrações nacionais e internacionais.",
            'location'   => 'Cidade do Rock — Anhembi',
            'address'    => 'Av. Olavo Fontoura, 1209',
            'city'       => 'São Paulo', 'state' => 'SP',
            'start_date' => now()->addDays(60)->setTime(14, 0),
            'end_date'   => now()->addDays(63)->setTime(0, 0),
            'status'     => EventStatus::PUBLISHED,
        ]);

        $tt5a = $this->makeTicketType($event5, 'Pista — 1 dia', 280.00, 5000, 3200);
        $tt5b = $this->makeTicketType($event5, 'Pista — 4 dias', 890.00, 2000, 1100);
        $tt5c = $this->makeTicketType($event5, 'Camarote', 1200.00, 300, 190);

        foreach (range(0, 9) as $i) {
            $tt = [$tt5a, $tt5b, $tt5c][$i % 3];
            $this->createPaidOrder($buyers[$i % count($buyers)], $event5, $tt, rand(1, 2));
        }

        // ─── Evento 6: Maratona SP (upcoming) ────────────────────────────────
        $event6 = $this->makeEvent($mainOrganizer, [
            'title'      => 'Maratona de São Paulo 2026',
            'slug'       => 'maratona-sp-2026',
            'description'=> "A mais tradicional maratona do Brasil. Percursos de 5km, 10km, 21km e 42km pelas principais avenidas de São Paulo.",
            'location'   => 'Praça da República',
            'address'    => 'Praça da República, s/n',
            'city'       => 'São Paulo', 'state' => 'SP',
            'start_date' => now()->addDays(45)->setTime(6, 0),
            'end_date'   => now()->addDays(45)->setTime(14, 0),
            'status'     => EventStatus::PUBLISHED,
        ]);

        $tt6a = $this->makeTicketType($event6, '5km', 69.90, 1000, 700);
        $tt6b = $this->makeTicketType($event6, '10km', 99.90, 800, 500);
        $tt6c = $this->makeTicketType($event6, '21km — Meia Maratona', 149.90, 500, 300);
        $tt6d = $this->makeTicketType($event6, '42km — Maratona Completa', 199.90, 200, 80);

        foreach (range(0, 7) as $i) {
            $tt = [$tt6a, $tt6b, $tt6c, $tt6d][$i % 4];
            $this->createPaidOrder($buyers[$i % count($buyers)], $event6, $tt, 1);
        }

        // ─── Evento 7: Noite do Humor (published) ────────────────────────────
        $event7 = $this->makeEvent($mainOrganizer, [
            'title'      => 'Noite do Humor SP — Temporada 3',
            'slug'       => 'noite-do-humor-sp-t3',
            'description'=> "Stand-up comedy com os melhores comediantes do Brasil. Uma noite de risadas garantidas no Teatro Renault.",
            'location'   => 'Teatro Renault',
            'address'    => 'Av. Brigadeiro Luís Antônio, 411',
            'city'       => 'São Paulo', 'state' => 'SP',
            'start_date' => now()->addDays(15)->setTime(20, 0),
            'end_date'   => now()->addDays(15)->setTime(23, 0),
            'status'     => EventStatus::PUBLISHED,
        ]);

        $tt7a = $this->makeTicketType($event7, 'Plateia', 120.00, 400, 280);
        $tt7b = $this->makeTicketType($event7, 'Plateia Premium', 200.00, 100, 60);

        foreach (range(0, 5) as $i) {
            $this->createPaidOrder($buyers[$i % count($buyers)], $event7, $i % 2 === 0 ? $tt7a : $tt7b, rand(1, 2));
        }

        // ─── Evento 8: Conferência Marketing Digital (published) ──────────────
        $event8 = $this->makeEvent($mainOrganizer, [
            'title'      => 'Marketing Digital Summit 2026',
            'slug'       => 'marketing-digital-summit-2026',
            'description'=> "O maior evento de marketing digital do Brasil. Cases de sucesso, tendências e ferramentas para profissionais de marketing.",
            'location'   => 'WTC Events Center',
            'address'    => 'Av. das Nações Unidas, 12551',
            'city'       => 'São Paulo', 'state' => 'SP',
            'start_date' => now()->addDays(20)->setTime(8, 0),
            'end_date'   => now()->addDays(21)->setTime(18, 0),
            'status'     => EventStatus::PUBLISHED,
        ]);

        $tt8a = $this->makeTicketType($event8, 'Ingresso Geral', 350.00, 600, 400);
        $tt8b = $this->makeTicketType($event8, 'Ingresso VIP', 750.00, 80, 40);

        foreach (range(0, 5) as $i) {
            $this->createPaidOrder($buyers[$i % count($buyers)], $event8, $i % 3 === 0 ? $tt8b : $tt8a, 1);
        }

        // ─── Evento 9: Festival Gastronômico (published) ──────────────────────
        $event9 = $this->makeEvent($mainOrganizer, [
            'title'      => 'Festival Gastronômico de SP 2026',
            'slug'       => 'festival-gastronomico-sp-2026',
            'description'=> "Três dias celebrando a gastronomia brasileira e internacional. Mais de 50 restaurantes, chefs estrelados, workshops culinários e shows musicais.",
            'location'   => 'Parque Villa-Lobos',
            'address'    => 'Av. Professor Fonseca Rodrigues, 2001',
            'city'       => 'São Paulo', 'state' => 'SP',
            'start_date' => now()->addDays(30)->setTime(12, 0),
            'end_date'   => now()->addDays(32)->setTime(22, 0),
            'status'     => EventStatus::PUBLISHED,
        ]);

        $tt9a = $this->makeTicketType($event9, 'Entrada — 1 dia', 45.00, 2000, 1200);
        $tt9b = $this->makeTicketType($event9, 'Passaporte — 3 dias', 110.00, 500, 280);

        foreach (range(0, 5) as $i) {
            $this->createPaidOrder($buyers[$i % count($buyers)], $event9, $i % 2 === 0 ? $tt9a : $tt9b, rand(1, 3));
        }

        // ─── Evento 10: Rascunho ──────────────────────────────────────────────
        $this->makeEvent($mainOrganizer, [
            'title'      => 'Expo Arte Contemporânea SP [RASCUNHO]',
            'slug'       => 'expo-arte-contemporanea-sp',
            'description'=> 'Exposição de arte contemporânea brasileira. Em preparação.',
            'location'   => 'MASP',
            'address'    => 'Av. Paulista, 1578',
            'city'       => 'São Paulo', 'state' => 'SP',
            'start_date' => now()->addDays(90)->setTime(10, 0),
            'end_date'   => now()->addDays(120)->setTime(18, 0),
            'status'     => EventStatus::DRAFT,
        ]);

        // ─── 40 eventos extras (factories) para main organizer ────────────────
        $extraEvents = Event::factory()->count(40)->published()->create([
            'organizer_id' => $mainOrganizer->id,
        ]);

        foreach ($extraEvents as $extraEvent) {
            $qty = rand(50, 500);
            $tt = TicketType::create([
                'event_id'    => $extraEvent->id,
                'name'        => collect(['Ingresso Geral', 'Pista', 'VIP', 'Standard', 'Meia-Entrada'])->random(),
                'description' => 'Ingresso para o evento.',
                'price'       => rand(0, 1) ? rand(30, 200) * 10 / 10 : 0,
                'quantity'    => $qty,
                'available'   => rand(0, $qty),
                'sale_start'  => now()->subDays(30),
                'sale_end'    => now()->addDays(30),
                'max_per_user'=> 10,
            ]);

            // 1–3 pedidos por evento extra
            $count = rand(1, 3);
            foreach (range(1, $count) as $j) {
                $this->createPaidOrder($buyers->random(), $extraEvent, $tt, rand(1, 2));
            }
        }

        // ─── Eventos do segundo organizador (Rio) ─────────────────────────────
        $rioEvents = [
            ['title' => 'Sunset Beats Ipanema', 'slug' => 'sunset-beats-ipanema-2026', 'location' => 'Quiosque do Arpoador', 'city' => 'Rio de Janeiro', 'state' => 'RJ', 'days' => 12, 'price' => 220.00],
            ['title' => 'Baile Charme Madureira', 'slug' => 'baile-charme-madureira-2026', 'location' => 'Madureira Park', 'city' => 'Rio de Janeiro', 'state' => 'RJ', 'days' => 5, 'price' => 80.00],
            ['title' => 'Rio Tech Day', 'slug' => 'rio-tech-day-2026', 'location' => 'Pier Mauá', 'city' => 'Rio de Janeiro', 'state' => 'RJ', 'days' => 18, 'price' => 150.00],
            ['title' => 'Festival de Jazz Lapa', 'slug' => 'festival-jazz-lapa-2026', 'location' => 'Arcos da Lapa', 'city' => 'Rio de Janeiro', 'state' => 'RJ', 'days' => 30, 'price' => 60.00],
            ['title' => 'Corrida Orla Carioca', 'slug' => 'corrida-orla-carioca-2026', 'location' => 'Praia de Copacabana', 'city' => 'Rio de Janeiro', 'state' => 'RJ', 'days' => 40, 'price' => 95.00],
        ];

        foreach ($rioEvents as $rd) {
            $rEvent = $this->makeEvent($rioOrganizer, [
                'title'      => $rd['title'],
                'slug'       => $rd['slug'],
                'description'=> fake()->paragraphs(2, true),
                'location'   => $rd['location'],
                'address'    => fake()->streetAddress(),
                'city'       => $rd['city'],
                'state'      => $rd['state'],
                'start_date' => now()->addDays($rd['days'])->setTime(15, 0),
                'end_date'   => now()->addDays($rd['days'])->setTime(22, 0),
                'status'     => EventStatus::PUBLISHED,
            ]);

            $rTt = $this->makeTicketType($rEvent, 'Pista', $rd['price'], 600, rand(200, 500));

            foreach (range(0, rand(2, 5)) as $i) {
                $this->createPaidOrder($buyers->random(), $rEvent, $rTt, rand(1, 2));
            }
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────────────────

    private function makeEvent(Organizer $organizer, array $data): Event
    {
        return Event::create(['organizer_id' => $organizer->id, 'banner' => null, ...$data]);
    }

    private function makeTicketType(
        Event $event,
        string $name,
        float $price,
        int $quantity,
        int $available,
        bool $past = false
    ): TicketType {
        return TicketType::create([
            'event_id'     => $event->id,
            'name'         => $name,
            'description'  => "{$name} para {$event->title}.",
            'price'        => $price,
            'quantity'     => $quantity,
            'available'    => $available,
            'sale_start'   => $past ? now()->subDays(60) : now()->subDays(20),
            'sale_end'     => $past ? now()->subDays(1) : now()->addDays(30),
            'max_per_user' => 10,
        ]);
    }

    private function addCollaborator(
        Event $event,
        User $inviter,
        ?User $user,
        string $status,
        ?string $email = null
    ): void {
        $expiresAt = $event->end_date ?? $event->start_date->copy()->addHours(24);

        EventCollaborator::create([
            'event_id'        => $event->id,
            'inviter_user_id' => $inviter->id,
            'invitee_email'   => $user?->email ?? $email,
            'user_id'         => $user?->id,
            'status'          => $status,
            'accepted_at'     => $status === 'active' ? now()->subDays(rand(1, 5)) : null,
            'expires_at'      => $expiresAt,
        ]);
    }

    private function createPaidOrder(
        User $user,
        Event $event,
        TicketType $ticketType,
        int $qty,
        ?float $unitPrice = null
    ): Order {
        $price = $unitPrice ?? $ticketType->price;
        $total = $price * $qty;
        $fee   = round($total * 0.05, 2);

        $order = Order::create([
            'user_id'          => $user->id,
            'event_id'         => $event->id,
            'status'           => OrderStatus::PAID,
            'total_amount'     => $total,
            'platform_fee'     => $fee,
            'organizer_amount' => $total - $fee,
            'expires_at'       => now()->addHours(24),
        ]);

        $orderItem = OrderItem::create([
            'order_id'       => $order->id,
            'ticket_type_id' => $ticketType->id,
            'quantity'       => $qty,
            'unit_price'     => $price,
        ]);

        for ($i = 0; $i < $qty; $i++) {
            $name = $this->names[$this->nameIdx % count($this->names)];
            $this->nameIdx++;

            $ticket = Ticket::create([
                'event_id'       => $event->id,
                'ticket_type_id' => $ticketType->id,
                'order_item_id'  => $orderItem->id,
                'ticket_code'    => 'TKT-' . strtoupper(Str::random(8)),
                'qr_code_payload'=> Str::uuid(),
                'status'         => TicketStatus::VALID,
            ]);

            Participant::create([
                'ticket_id' => $ticket->id,
                'name'      => $name,
                'email'     => Str::slug($name) . '.' . Str::random(4) . '@email.com',
                'phone'     => '(11) 9' . rand(1000, 9999) . '-' . rand(1000, 9999),
                'document'  => rand(100, 999) . '.' . rand(100, 999) . '.' . rand(100, 999) . '-' . rand(10, 99),
            ]);
        }

        $order->load('items');

        return $order;
    }
}
