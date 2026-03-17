<?php

namespace Database\Seeders;

use App\Enums\EventStatus;
use App\Enums\OrderStatus;
use App\Enums\TicketStatus;
use App\Models\Checkin;
use App\Models\CustomField;
use App\Models\Event;
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
    public function run(): void
    {
        // ─── Usuário principal (você) ───────────────────────────────────────
        $mainUser = User::create([
            'name' => 'João Filipi',
            'email' => 'joao@taketicket.com.br',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        // ─── Organizador principal ──────────────────────────────────────────
        $mainOrganizer = Organizer::create([
            'user_id' => $mainUser->id,
            'name' => 'TakeTicket Eventos',
            'slug' => 'taketicket-eventos',
            'description' => 'Organizadora líder em eventos culturais, esportivos e corporativos no Brasil.',
            'document' => '12.345.678/0001-99',
            'phone' => '(11) 98765-4321',
            'address' => 'Av. Paulista, 1000',
            'city' => 'São Paulo',
            'state' => 'SP',
            'postal_code' => '01310-100',
        ]);

        // ─── Outros compradores ─────────────────────────────────────────────
        $buyers = collect([
            ['name' => 'Ana Souza', 'email' => 'ana@email.com'],
            ['name' => 'Carlos Mendes', 'email' => 'carlos@email.com'],
            ['name' => 'Fernanda Lima', 'email' => 'fernanda@email.com'],
            ['name' => 'Ricardo Pereira', 'email' => 'ricardo@email.com'],
            ['name' => 'Mariana Costa', 'email' => 'mariana@email.com'],
        ])->map(fn ($data) => User::create([
            ...$data,
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]));

        // ─── EVENTO 1: Corrida da Noite (Publicado, em breve) ──────────────
        $event1 = $this->createEvent($mainOrganizer, [
            'title' => 'Corrida da Noite SP 2026',
            'slug' => 'corrida-da-noite-sp-2026',
            'description' => "A maior corrida noturna de São Paulo está de volta! Percursos de 5km e 10km pelo coração da cidade, com iluminação especial e DJ ao vivo na largada.\n\nInclui: chip de cronometragem, camiseta oficial, medalha de finisher e kit hidratação no percurso.\n\nCategoria única: adultos acima de 16 anos.",
            'location' => 'Parque Ibirapuera',
            'address' => 'Av. Pedro Álvares Cabral, s/n',
            'city' => 'São Paulo',
            'state' => 'SP',
            'start_date' => now()->addDays(25)->setTime(20, 0),
            'end_date' => now()->addDays(25)->setTime(23, 30),
            'status' => EventStatus::PUBLISHED,
        ]);

        $tt1a = TicketType::create([
            'event_id' => $event1->id,
            'name' => 'Corrida 5km',
            'description' => 'Percurso de 5 quilômetros pelo Ibirapuera',
            'price' => 89.90,
            'quantity' => 500,
            'available' => 487,
            'sale_start' => now()->subDays(10),
            'sale_end' => now()->addDays(24),
            'max_per_user' => 5,
        ]);

        $tt1b = TicketType::create([
            'event_id' => $event1->id,
            'name' => 'Corrida 10km',
            'description' => 'Percurso de 10 quilômetros com maior desafio',
            'price' => 119.90,
            'quantity' => 300,
            'available' => 289,
            'sale_start' => now()->subDays(10),
            'sale_end' => now()->addDays(24),
            'max_per_user' => 3,
        ]);

        CustomField::create(['event_id' => $event1->id, 'label' => 'Tamanho da camiseta', 'type' => 'select', 'options' => json_encode(['P', 'M', 'G', 'GG']), 'required' => true, 'position' => 1]);
        CustomField::create(['event_id' => $event1->id, 'label' => 'Contato de emergência (nome e telefone)', 'type' => 'text', 'options' => null, 'required' => true, 'position' => 2]);

        // Pedidos para o evento 1
        $this->createPaidOrder($buyers[0], $event1, $tt1a, 2);
        $this->createPaidOrder($buyers[1], $event1, $tt1b, 1);
        $this->createPaidOrder($buyers[2], $event1, $tt1a, 1);
        $this->createPaidOrder($buyers[3], $event1, $tt1b, 2);
        $this->createPaidOrder($mainUser, $event1, $tt1a, 1); // o próprio organizador comprou

        // ─── EVENTO 2: Festival de Música (Publicado, próximo fim de semana) ─
        $event2 = $this->createEvent($mainOrganizer, [
            'title' => 'Festival Indie Sampa 2026',
            'slug' => 'festival-indie-sampa-2026',
            'description' => "Dois dias de muito rock, indie e pop alternativo no centro de SP. Mais de 15 bandas nacionais e internacionais em 3 palcos simultâneos.\n\nDia 1 (sexta): palco principal com shows das 18h às 02h\nDia 2 (sábado): todos os palcos das 14h às 02h\n\nProibida entrada de menores de 18 anos após as 22h.",
            'location' => 'Espaço das Américas',
            'address' => 'Rua Tagipuru, 795',
            'city' => 'São Paulo',
            'state' => 'SP',
            'start_date' => now()->addDays(8)->setTime(18, 0),
            'end_date' => now()->addDays(9)->setTime(2, 0),
            'status' => EventStatus::PUBLISHED,
        ]);

        $tt2a = TicketType::create([
            'event_id' => $event2->id,
            'name' => 'Pista — 1 dia',
            'description' => 'Acesso pista por 1 dia (sexta ou sábado)',
            'price' => 180.00,
            'quantity' => 1000,
            'available' => 634,
            'sale_start' => now()->subDays(20),
            'sale_end' => now()->addDays(7),
            'max_per_user' => 4,
        ]);

        $tt2b = TicketType::create([
            'event_id' => $event2->id,
            'name' => 'Pista — 2 dias',
            'description' => 'Acesso pista sexta e sábado',
            'price' => 300.00,
            'quantity' => 500,
            'available' => 312,
            'sale_start' => now()->subDays(20),
            'sale_end' => now()->addDays(7),
            'max_per_user' => 4,
        ]);

        $tt2c = TicketType::create([
            'event_id' => $event2->id,
            'name' => 'VIP — 2 dias',
            'description' => 'Área VIP com open bar e camarote',
            'price' => 650.00,
            'quantity' => 100,
            'available' => 78,
            'sale_start' => now()->subDays(20),
            'sale_end' => now()->addDays(7),
            'max_per_user' => 2,
        ]);

        $this->createPaidOrder($buyers[0], $event2, $tt2b, 2);
        $this->createPaidOrder($buyers[1], $event2, $tt2c, 1);
        $this->createPaidOrder($buyers[2], $event2, $tt2a, 3);
        $this->createPaidOrder($buyers[4], $event2, $tt2b, 1);

        // ─── EVENTO 3: Workshop de Fotografia (Publicado, gratuito) ──────────
        $event3 = $this->createEvent($mainOrganizer, [
            'title' => 'Workshop de Fotografia Urbana',
            'slug' => 'workshop-fotografia-urbana-2026',
            'description' => "Aprenda técnicas de fotografia urbana com profissionais renomados. O workshop acontece nas ruas do centro histórico de São Paulo.\n\nO que você vai aprender:\n• Composição e enquadramento em ambientes urbanos\n• Fotografia com luz natural\n• Edição básica no celular\n\nTraga seu celular ou câmera. Vagas limitadas!",
            'location' => 'Museu da Luz',
            'address' => 'Praça da Luz, s/n',
            'city' => 'São Paulo',
            'state' => 'SP',
            'start_date' => now()->addDays(3)->setTime(9, 0),
            'end_date' => now()->addDays(3)->setTime(17, 0),
            'status' => EventStatus::PUBLISHED,
        ]);

        $tt3a = TicketType::create([
            'event_id' => $event3->id,
            'name' => 'Ingresso Gratuito',
            'description' => 'Inscrição gratuita — vagas limitadas',
            'price' => 0,
            'quantity' => 30,
            'available' => 12,
            'sale_start' => now()->subDays(5),
            'sale_end' => now()->addDays(2),
            'max_per_user' => 1,
        ]);

        $this->createPaidOrder($buyers[0], $event3, $tt3a, 1, 0);
        $this->createPaidOrder($buyers[2], $event3, $tt3a, 1, 0);
        $this->createPaidOrder($buyers[3], $event3, $tt3a, 1, 0);

        // ─── EVENTO 4: Conferência Tech (Publicado, com check-in já feito) ──
        $event4 = $this->createEvent($mainOrganizer, [
            'title' => 'TechConf Brasil 2026',
            'slug' => 'techconf-brasil-2026',
            'description' => "A maior conferência de tecnologia do Brasil reúne líderes de mercado, startups e entusiastas de tecnologia para dois dias intensos de palestras, workshops e networking.\n\nTemas: IA Generativa, Cloud Computing, Segurança, Desenvolvimento Mobile, DevOps e muito mais.",
            'location' => 'Transamerica Expo Center',
            'address' => 'Av. Dr. Mário Vilas Boas Rodrigues, 387',
            'city' => 'São Paulo',
            'state' => 'SP',
            'start_date' => now()->subDays(2)->setTime(9, 0),
            'end_date' => now()->subDays(1)->setTime(18, 0),
            'status' => EventStatus::PUBLISHED,
        ]);

        $tt4a = TicketType::create([
            'event_id' => $event4->id,
            'name' => 'Palestrante / Expositor',
            'description' => 'Acesso completo para palestrantes',
            'price' => 0,
            'quantity' => 50,
            'available' => 42,
            'sale_start' => now()->subDays(60),
            'sale_end' => now()->subDays(3),
            'max_per_user' => 1,
        ]);

        $tt4b = TicketType::create([
            'event_id' => $event4->id,
            'name' => 'Ingresso Geral — 2 dias',
            'description' => 'Acesso a todas as palestras e workshops',
            'price' => 490.00,
            'quantity' => 800,
            'available' => 0,
            'sale_start' => now()->subDays(60),
            'sale_end' => now()->subDays(3),
            'max_per_user' => 2,
        ]);

        $tt4c = TicketType::create([
            'event_id' => $event4->id,
            'name' => 'VIP — 2 dias',
            'description' => 'Acesso VIP + jantar de networking',
            'price' => 990.00,
            'quantity' => 100,
            'available' => 5,
            'sale_start' => now()->subDays(60),
            'sale_end' => now()->subDays(3),
            'max_per_user' => 1,
        ]);

        // Pedidos com check-in realizado
        $order4a = $this->createPaidOrder($buyers[0], $event4, $tt4b, 1);
        $order4b = $this->createPaidOrder($buyers[1], $event4, $tt4b, 1);
        $order4c = $this->createPaidOrder($buyers[2], $event4, $tt4c, 1);

        // Marcar alguns como check-in feito
        foreach ([$order4a, $order4b, $order4c] as $order) {
            $tickets = Ticket::whereIn('order_item_id', $order->items->pluck('id'))->get();
            foreach ($tickets as $ticket) {
                $ticket->update(['status' => TicketStatus::USED, 'checked_in_at' => $order->event->start_date->addMinutes(rand(5, 60))]);
                Checkin::create([
                    'ticket_id' => $ticket->id,
                    'checked_by' => $mainUser->id,
                    'checked_at' => $ticket->checked_in_at,
                ]);
            }
        }

        // ─── EVENTO 5: Rascunho (só visível no dashboard) ────────────────────
        $this->createEvent($mainOrganizer, [
            'title' => 'Noite do Humor SP — Temporada 3',
            'slug' => 'noite-do-humor-sp-t3',
            'description' => 'Stand-up comedy com os melhores comediantes do Brasil. Em breve mais informações.',
            'location' => 'Teatro Renault',
            'address' => 'Av. Brigadeiro Luís Antônio, 411',
            'city' => 'São Paulo',
            'state' => 'SP',
            'start_date' => now()->addDays(45)->setTime(20, 0),
            'end_date' => now()->addDays(45)->setTime(23, 0),
            'status' => EventStatus::DRAFT,
        ]);

        // ─── EVENTO 6: Rio de Janeiro (outro organizador) ─────────────────────
        $rioUser = User::create([
            'name' => 'Bianca Rocha',
            'email' => 'bianca@rioeventos.com.br',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        $rioOrganizer = Organizer::create([
            'user_id' => $rioUser->id,
            'name' => 'Rio Eventos',
            'slug' => 'rio-eventos',
            'description' => 'Eventos culturais no Rio de Janeiro.',
            'document' => '98.765.432/0001-11',
            'phone' => '(21) 97654-3210',
            'address' => 'Av. Atlântica, 1702',
            'city' => 'Rio de Janeiro',
            'state' => 'RJ',
            'postal_code' => '22021-001',
        ]);

        $event6 = $this->createEvent($rioOrganizer, [
            'title' => 'Sunset Beats Ipanema',
            'slug' => 'sunset-beats-ipanema-2026',
            'description' => "Uma tarde de música eletrônica com os melhores DJs nacionais na Praia de Ipanema.\n\nOpen bar de 17h às 22h. Classificação: 18 anos.",
            'location' => 'Quiosque do Arpoador',
            'address' => 'Praia de Ipanema — Posto 7',
            'city' => 'Rio de Janeiro',
            'state' => 'RJ',
            'start_date' => now()->addDays(12)->setTime(15, 0),
            'end_date' => now()->addDays(12)->setTime(22, 0),
            'status' => EventStatus::PUBLISHED,
        ]);

        $tt6a = TicketType::create([
            'event_id' => $event6->id,
            'name' => 'Pista',
            'description' => 'Acesso pista com open bar',
            'price' => 220.00,
            'quantity' => 600,
            'available' => 398,
            'sale_start' => now()->subDays(5),
            'sale_end' => now()->addDays(11),
            'max_per_user' => 4,
        ]);

        $this->createPaidOrder($buyers[3], $event6, $tt6a, 2);
        $this->createPaidOrder($buyers[4], $event6, $tt6a, 1);
    }

    private function createEvent(Organizer $organizer, array $data): Event
    {
        return Event::create([
            'organizer_id' => $organizer->id,
            'banner' => null,
            ...$data,
        ]);
    }

    private function createPaidOrder(User $user, Event $event, TicketType $ticketType, int $qty, ?float $unitPrice = null): Order
    {
        $price = $unitPrice ?? $ticketType->price;
        $total = $price * $qty;
        $fee = round($total * 0.05, 2);

        $order = Order::create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'status' => OrderStatus::PAID,
            'total_amount' => $total,
            'platform_fee' => $fee,
            'organizer_amount' => $total - $fee,
            'expires_at' => now()->addHours(24),
        ]);

        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'ticket_type_id' => $ticketType->id,
            'quantity' => $qty,
            'unit_price' => $price,
        ]);

        $brazilianNames = [
            ['name' => 'Ana Paula Souza', 'email' => 'ana.paula.' . Str::random(4) . '@email.com', 'phone' => '(11) 98' . rand(100, 999) . '-' . rand(1000, 9999), 'doc' => rand(100, 999) . '.' . rand(100, 999) . '.' . rand(100, 999) . '-' . rand(10, 99)],
            ['name' => 'Carlos Eduardo Mendes', 'email' => 'carlos.e.' . Str::random(4) . '@email.com', 'phone' => '(21) 97' . rand(100, 999) . '-' . rand(1000, 9999), 'doc' => rand(100, 999) . '.' . rand(100, 999) . '.' . rand(100, 999) . '-' . rand(10, 99)],
            ['name' => 'Fernanda Lima', 'email' => 'fernanda.' . Str::random(4) . '@email.com', 'phone' => '(31) 99' . rand(100, 999) . '-' . rand(1000, 9999), 'doc' => rand(100, 999) . '.' . rand(100, 999) . '.' . rand(100, 999) . '-' . rand(10, 99)],
            ['name' => 'Ricardo Alves Pereira', 'email' => 'r.pereira.' . Str::random(4) . '@email.com', 'phone' => '(85) 98' . rand(100, 999) . '-' . rand(1000, 9999), 'doc' => rand(100, 999) . '.' . rand(100, 999) . '.' . rand(100, 999) . '-' . rand(10, 99)],
            ['name' => 'Mariana Costa Silva', 'email' => 'mariana.' . Str::random(4) . '@email.com', 'phone' => '(41) 97' . rand(100, 999) . '-' . rand(1000, 9999), 'doc' => rand(100, 999) . '.' . rand(100, 999) . '.' . rand(100, 999) . '-' . rand(10, 99)],
            ['name' => 'João Victor Santos', 'email' => 'joao.v.' . Str::random(4) . '@email.com', 'phone' => '(11) 96' . rand(100, 999) . '-' . rand(1000, 9999), 'doc' => rand(100, 999) . '.' . rand(100, 999) . '.' . rand(100, 999) . '-' . rand(10, 99)],
        ];

        for ($i = 0; $i < $qty; $i++) {
            $p = $brazilianNames[$i % count($brazilianNames)];

            $ticket = Ticket::create([
                'event_id' => $event->id,
                'ticket_type_id' => $ticketType->id,
                'order_item_id' => $orderItem->id,
                'ticket_code' => 'TKT-' . strtoupper(Str::random(8)),
                'qr_code_payload' => Str::uuid(),
                'status' => TicketStatus::VALID,
            ]);

            Participant::create([
                'ticket_id' => $ticket->id,
                'name' => $p['name'],
                'email' => $p['email'],
                'phone' => $p['phone'],
                'document' => $p['doc'],
            ]);
        }

        $order->load('items');

        return $order;
    }
}
