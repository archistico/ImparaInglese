<?php

namespace App\DataFixtures;

use App\Entity\Contesto;
use App\Entity\Direzione;
use App\Entity\Espressione;
use App\Entity\Frase;
use App\Entity\Lingua;
use App\Entity\Livello;
use App\Entity\Traduzione;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // ---------------------------------------------
        // Seed "base"
        // ---------------------------------------------
        $it = (new Lingua())->setDescrizione('Italiano');
        $en = (new Lingua())->setDescrizione('Inglese');
        $manager->persist($it);
        $manager->persist($en);

        $base = (new Livello())->setDescrizione('Base');
        $manager->persist($base);
        
        $intermedio = (new Livello())->setDescrizione('Intermedio');
        $manager->persist($intermedio);
        
        $avanzato = (new Livello())->setDescrizione('Avanzato');
        $manager->persist($avanzato);
        $manager->persist($base);

        $dirItEn = (new Direzione())
            ->setDescrizione('Italiano -> Inglese')
            ->setLinguaPartenza($it)
            ->setLinguaArrivo($en);
        $manager->persist($dirItEn);

        $dirEnIt = (new Direzione())
            ->setDescrizione('Inglese -> Italiano')
            ->setLinguaPartenza($en)
            ->setLinguaArrivo($it);
        $manager->persist($dirEnIt);

        // ---------------------------------------------
        // Contesti + Frasi (estratte dal tuo CSV)
        // ---------------------------------------------
        $dataset = [
            'Saluti e buone maniere' => [
                ['it' => 'Dopo di te', 'it_info' => 'per far passare qualcuno', 'en' => 'After you!'],
                ['it' => 'Buon pomeriggio', 'it_info' => 'informale', 'en' => 'Afternoon!'],
                ['it' => 'E tu?', 'en' => 'And you?'],
                ['it' => 'Prego', 'it_info' => 'non c\'è di che', 'en' => 'Anytime!'],
                ['it' => 'Scuse accettate', 'en' => 'Apology accepted'],
                ['it' => 'Salute! Dopo uno starnuto', 'en' => 'Bless you!'],
                ['it' => 'Addio', 'it_info' => 'informale', 'en' => 'Bye! / Bye-bye!'],
                ['it' => 'Ciao, quando te ne vai, molto informale', 'en' => 'Catch you later!'],
                ['it' => 'Tutto ok', 'en' => 'Doing fine'],
                ['it' => 'Nessun problema', 'it_info' => 'informale', 'en' => 'Don\'t worry about it'],
                ['it' => 'Buonasera', 'it_info' => 'più informale', 'en' => 'Evening!'],
                ['it' => 'Scusa', 'it_info' => 'per disturbare o chiedere permesso', 'en' => 'Excuse me'],
                ['it' => 'Addio', 'it_info' => 'molto formale o drammatico', 'en' => 'Farewell!'],
                ['it' => 'Bene', 'it_info' => 'più naturale', 'en' => 'Fine / I\'m good / I\'m well'],
                ['it' => 'Mi scusi', 'it_info' => 'molto formale', 'en' => 'Forgive me'],
                ['it' => 'Buon pomeriggio', 'en' => 'Good afternoon'],
                ['it' => 'Salve', 'it_info' => 'saluto formale', 'en' => 'Good day'],
                ['it' => 'Buonasera', 'en' => 'Good evening'],
                ['it' => 'Buongiorno', 'en' => 'Good morning'],
                ['it' => 'Buonanotte', 'en' => 'Good night'],
                ['it' => 'Ciao', 'en' => 'Hello / Hi'],
            ],

            'Domande e risposte comuni' => [
                ['it' => 'Come stai?', 'en' => 'How are you?'],
                ['it' => 'Come ti chiami?', 'en' => 'What\'s your name?'],
                ['it' => 'Piacere', 'en' => 'Nice to meet you'],
                ['it' => 'Da dove vieni?', 'en' => 'Where are you from?'],
                ['it' => 'Dove vivi?', 'en' => 'Where do you live?'],
                ['it' => 'Che lavoro fai?', 'en' => 'What do you do?'],
                ['it' => 'Puoi aiutarmi?', 'en' => 'Can you help me?'],
                ['it' => 'Puoi ripetere?', 'en' => 'Can you repeat that?'],
                ['it' => 'Puoi parlare più lentamente?', 'en' => 'Can you speak more slowly?'],
                ['it' => 'Cosa significa?', 'en' => 'What does it mean?'],
                ['it' => 'Come si dice in inglese?', 'en' => 'How do you say it in English?'],
                ['it' => 'Dove sono i bagni?', 'en' => 'Where is the bathroom?'],
                ['it' => 'Quanto costa?', 'en' => 'How much is it?'],
                ['it' => 'Che ore sono?', 'en' => 'What time is it?'],
                ['it' => 'Che giorno è oggi?', 'en' => 'What day is it today?'],
                ['it' => 'Che data è oggi?', 'en' => 'What\'s the date today?'],
                ['it' => 'Hai tempo?', 'en' => 'Do you have time?'],
                ['it' => 'Sei sicuro?', 'en' => 'Are you sure?'],
                ['it' => 'Va bene', 'en' => 'All right / OK'],
                ['it' => 'Non lo so', 'en' => 'I don\'t know'],
                ['it' => 'Forse', 'en' => 'Maybe'],
                ['it' => 'Perché?', 'en' => 'Why?'],
                ['it' => 'Quando?', 'en' => 'When?'],
                ['it' => 'Dove?', 'en' => 'Where?'],
                ['it' => 'Chi?', 'en' => 'Who?'],
            ],

            'Come sto' => [
                ['it' => 'Sto bene', 'en' => 'I\'m fine'],
                ['it' => 'Sto molto bene', 'en' => 'I\'m very well'],
                ['it' => 'Così così', 'en' => 'So-so'],
                ['it' => 'Sono stanco', 'en' => 'I\'m tired'],
                ['it' => 'Sono stressato', 'en' => 'I\'m stressed'],
                ['it' => 'Sono felice', 'en' => 'I\'m happy'],
                ['it' => 'Sono triste', 'en' => 'I\'m sad'],
                ['it' => 'Sono nervoso', 'en' => 'I\'m nervous'],
                ['it' => 'Ho fame', 'en' => 'I\'m hungry'],
                ['it' => 'Ho sete', 'en' => 'I\'m thirsty'],
                ['it' => 'Ho freddo', 'en' => 'I\'m cold'],
                ['it' => 'Ho caldo', 'en' => 'I\'m hot'],
                ['it' => 'Mi annoio', 'en' => 'I\'m bored'],
                ['it' => 'Sono emozionato', 'en' => 'I\'m excited'],
                ['it' => 'Mi sento meglio', 'en' => 'I feel better'],
                ['it' => 'Mi sento peggio', 'en' => 'I feel worse'],
                ['it' => 'Sono preoccupato', 'en' => 'I\'m worried'],
                ['it' => 'Sono malato', 'en' => 'I\'m sick'],
                ['it' => 'Ho mal di testa', 'en' => 'I have a headache'],
                ['it' => 'Ho mal di gola', 'en' => 'I have a sore throat'],
                ['it' => 'Ho la febbre', 'en' => 'I have a fever'],
                ['it' => 'Non mi sento bene', 'en' => 'I don\'t feel well'],
                ['it' => 'Mi fa male qui', 'en' => 'It hurts here'],
                ['it' => 'Sono allergico', 'en' => 'I\'m allergic'],
                ['it' => 'Sono esausto', 'en' => 'I\'m exhausted'],
            ],

            'Indicazioni' => [
                ['it' => 'Dov\'è...?', 'en' => 'Where is...?'],
                ['it' => 'Mi scusi, dov\'è...?', 'en' => 'Excuse me, where is...?'],
                ['it' => 'Come arrivo a...?', 'en' => 'How do I get to...?'],
                ['it' => 'È lontano?', 'en' => 'Is it far?'],
                ['it' => 'È vicino?', 'en' => 'Is it near?'],
                ['it' => 'Vai dritto', 'en' => 'Go straight'],
                ['it' => 'Gira a sinistra', 'en' => 'Turn left'],
                ['it' => 'Gira a destra', 'en' => 'Turn right'],
                ['it' => 'Alla fine della strada', 'en' => 'At the end of the street'],
                ['it' => 'All\'angolo', 'en' => 'On the corner'],
                ['it' => 'Di fronte', 'en' => 'Opposite'],
                ['it' => 'Accanto', 'en' => 'Next to'],
                ['it' => 'Tra', 'en' => 'Between'],
                ['it' => 'Attraversa la strada', 'en' => 'Cross the street'],
                ['it' => 'Prendi la prima a sinistra', 'en' => 'Take the first left'],
                ['it' => 'Prendi la seconda a destra', 'en' => 'Take the second right'],
                ['it' => 'Segui i cartelli', 'en' => 'Follow the signs'],
                ['it' => 'Mi sono perso', 'en' => 'I\'m lost'],
                ['it' => 'Puoi mostrarmelo sulla mappa?', 'en' => 'Can you show it to me on the map?'],
                ['it' => 'Quanto ci vuole a piedi?', 'en' => 'How long does it take on foot?'],
                ['it' => 'Quanto ci vuole in macchina?', 'en' => 'How long does it take by car?'],
                ['it' => 'È da questa parte', 'en' => 'It\'s this way'],
                ['it' => 'È dall\'altra parte', 'en' => 'It\'s that way'],
                ['it' => 'È a destra', 'en' => 'It\'s on the right'],
                ['it' => 'È a sinistra', 'en' => 'It\'s on the left'],
            ],

            'Ristoranti / Mangiare' => [
                ['it' => 'Vorrei un tavolo per due', 'en' => 'A table for two, please'],
                ['it' => 'Posso vedere il menù?', 'en' => 'Can I see the menu?'],
                ['it' => 'Vorrei ordinare', 'en' => 'I would like to order'],
                ['it' => 'Cosa mi consiglia?', 'en' => 'What do you recommend?'],
                ['it' => 'Sono vegetariano', 'en' => 'I\'m vegetarian'],
                ['it' => 'Sono vegano', 'en' => 'I\'m vegan'],
                ['it' => 'Sono allergico a...', 'en' => 'I\'m allergic to...'],
                ['it' => 'Senza glutine, per favore', 'en' => 'Gluten-free, please'],
                ['it' => 'Acqua naturale / frizzante', 'en' => 'Still / sparkling water'],
                ['it' => 'Un caffè, per favore', 'en' => 'A coffee, please'],
                ['it' => 'Il conto, per favore', 'en' => 'The bill, please'],
                ['it' => 'Posso pagare con carta?', 'en' => 'Can I pay by card?'],
                ['it' => 'È incluso il servizio?', 'en' => 'Is service included?'],
                ['it' => 'È delizioso', 'en' => 'It\'s delicious'],
                ['it' => 'Sono pieno', 'en' => 'I\'m full'],
                ['it' => 'Da asporto, per favore', 'en' => 'Take away, please'],
                ['it' => 'Non troppo piccante', 'en' => 'Not too spicy'],
            ],

            'Shopping / Pagare per qualcosa' => [
                ['it' => 'Quanto costa?', 'en' => 'How much is it?'],
                ['it' => 'Avete questa taglia?', 'en' => 'Do you have this size?'],
                ['it' => 'Posso provarlo?', 'en' => 'Can I try it on?'],
                ['it' => 'È troppo caro', 'en' => 'It\'s too expensive'],
                ['it' => 'Avete uno sconto?', 'en' => 'Do you have a discount?'],
                ['it' => 'Accettate carte?', 'en' => 'Do you accept cards?'],
                ['it' => 'Posso pagare in contanti', 'en' => 'I can pay in cash'],
                ['it' => 'Posso avere la ricevuta?', 'en' => 'Can I have the receipt?'],
                ['it' => 'Dove sono i camerini?', 'en' => 'Where are the fitting rooms?'],
                ['it' => 'Sto solo guardando', 'en' => 'I\'m just looking'],
                ['it' => 'Mi piace', 'en' => 'I like it'],
                ['it' => 'Non mi piace', 'en' => 'I don\'t like it'],
                ['it' => 'Lo prendo', 'en' => 'I\'ll take it'],
                ['it' => 'Posso restituirlo?', 'en' => 'Can I return it?'],
                ['it' => 'C\'è la garanzia?', 'en' => 'Is there a warranty?'],
                ['it' => 'Mi serve una borsa', 'en' => 'I need a bag'],
            ],

            'Trasporti' => [
                ['it' => 'Dov\'è la stazione?', 'en' => 'Where is the station?'],
                ['it' => 'Dov\'è la fermata dell\'autobus?', 'en' => 'Where is the bus stop?'],
                ['it' => 'Un biglietto, per favore', 'en' => 'One ticket, please'],
                ['it' => 'Andata e ritorno', 'en' => 'Round trip'],
                ['it' => 'Solo andata', 'en' => 'One way'],
                ['it' => 'A che ora parte?', 'en' => 'What time does it leave?'],
                ['it' => 'A che ora arriva?', 'en' => 'What time does it arrive?'],
                ['it' => 'È in ritardo?', 'en' => 'Is it delayed?'],
                ['it' => 'Qual è il binario?', 'en' => 'Which platform is it?'],
                ['it' => 'Devo cambiare?', 'en' => 'Do I need to change?'],
                ['it' => 'Dov\'è l\'aeroporto?', 'en' => 'Where is the airport?'],
                ['it' => 'Quanto costa un taxi per...?', 'en' => 'How much is a taxi to...?'],
                ['it' => 'Può fermarsi qui, per favore?', 'en' => 'Can you stop here, please?'],
                ['it' => 'È questo il posto giusto?', 'en' => 'Is this the right place?'],
            ],

            'Hotel / Ostello / Alloggio' => [
                ['it' => 'Ho una prenotazione', 'en' => 'I have a reservation'],
                ['it' => 'Vorrei una camera', 'en' => 'I would like a room'],
                ['it' => 'Per quante notti?', 'en' => 'For how many nights?'],
                ['it' => 'Una notte', 'en' => 'One night'],
                ['it' => 'Due notti', 'en' => 'Two nights'],
                ['it' => 'Con colazione inclusa?', 'en' => 'Is breakfast included?'],
                ['it' => 'A che ora è il check-in?', 'en' => 'What time is check-in?'],
                ['it' => 'A che ora è il check-out?', 'en' => 'What time is check-out?'],
                ['it' => 'Dov\'è la mia camera?', 'en' => 'Where is my room?'],
                ['it' => 'Mi serve la password del Wi-Fi', 'en' => 'I need the Wi-Fi password'],
                ['it' => 'C\'è una cassaforte?', 'en' => 'Is there a safe?'],
                ['it' => 'Posso lasciare i bagagli?', 'en' => 'Can I leave my luggage?'],
                ['it' => 'C\'è un problema con la camera', 'en' => 'There is a problem with the room'],
            ],

            'Medico / Ospedale' => [
                ['it' => 'Ho bisogno di un medico', 'en' => 'I need a doctor'],
                ['it' => 'Ho bisogno di aiuto', 'en' => 'I need help'],
                ['it' => 'Mi fa male qui', 'en' => 'It hurts here'],
                ['it' => 'Ho la febbre', 'en' => 'I have a fever'],
                ['it' => 'Ho mal di testa', 'en' => 'I have a headache'],
                ['it' => 'Ho mal di stomaco', 'en' => 'I have a stomachache'],
                ['it' => 'Ho mal di gola', 'en' => 'I have a sore throat'],
                ['it' => 'Sono allergico a...', 'en' => 'I\'m allergic to...'],
                ['it' => 'Prendo questo farmaco', 'en' => 'I take this medicine'],
                ['it' => 'Dove è il pronto soccorso?', 'en' => 'Where is the emergency room?'],
                ['it' => 'Serve una ricetta?', 'en' => 'Do I need a prescription?'],
                ['it' => 'Che cosa devo fare?', 'en' => 'What should I do?'],
                ['it' => 'Posso avere un appuntamento?', 'en' => 'Can I have an appointment?'],
                ['it' => 'Mi sento meglio', 'en' => 'I feel better'],
            ],

            'Banca e soldi' => [
                ['it' => 'Dov\'è un bancomat?', 'en' => 'Where is an ATM?'],
                ['it' => 'Vorrei prelevare', 'en' => 'I would like to withdraw money'],
                ['it' => 'Vorrei cambiare dei soldi', 'en' => 'I would like to exchange money'],
                ['it' => 'Quanto costa?', 'en' => 'How much is it?'],
                ['it' => 'Accettate carte?', 'en' => 'Do you accept cards?'],
                ['it' => 'Posso pagare con carta?', 'en' => 'Can I pay by card?'],
                ['it' => 'In contanti', 'en' => 'In cash'],
                ['it' => 'Mi serve una ricevuta', 'en' => 'I need a receipt'],
                ['it' => 'Ho perso la carta', 'en' => 'I lost my card'],
                ['it' => 'La carta non funziona', 'en' => 'The card doesn\'t work'],
                ['it' => 'Ho bisogno di assistenza', 'en' => 'I need assistance'],
                ['it' => 'Qual è il tasso di cambio?', 'en' => 'What\'s the exchange rate?'],
                ['it' => 'È troppo caro', 'en' => 'It\'s too expensive'],
                ['it' => 'Posso avere lo scontrino?', 'en' => 'Can I have the receipt?'],
            ],

            'Polizia' => [
                ['it' => 'Ho bisogno della polizia', 'en' => 'I need the police'],
                ['it' => 'Mi hanno rubato...', 'en' => 'They stole my...'],
                ['it' => 'Ho perso il passaporto', 'en' => 'I lost my passport'],
                ['it' => 'Ho perso il portafoglio', 'en' => 'I lost my wallet'],
                ['it' => 'Sono stato derubato', 'en' => 'I was robbed'],
                ['it' => 'Dov\'è la stazione di polizia?', 'en' => 'Where is the police station?'],
                ['it' => 'Voglio fare una denuncia', 'en' => 'I want to report it'],
                ['it' => 'Può aiutarmi?', 'en' => 'Can you help me?'],
                ['it' => 'È un\'emergenza', 'en' => 'It\'s an emergency'],
                ['it' => 'Conosco questa persona', 'en' => 'I know this person'],
                ['it' => 'Non conosco questa persona', 'en' => 'I don\'t know this person'],
                ['it' => 'Posso chiamare qualcuno?', 'en' => 'Can I call someone?'],
                ['it' => 'Mi serve un interprete', 'en' => 'I need an interpreter'],
            ],

            'Data e ora' => [
                ['it' => 'Che ore sono?', 'en' => 'What time is it?'],
                ['it' => 'È l\'una', 'en' => 'It\'s one o\'clock'],
                ['it' => 'Sono le due', 'en' => 'It\'s two o\'clock'],
                ['it' => 'Sono le tre', 'en' => 'It\'s three o\'clock'],
                ['it' => 'È mezzogiorno', 'en' => 'It\'s noon'],
                ['it' => 'È mezzanotte', 'en' => 'It\'s midnight'],
                ['it' => 'Che giorno è oggi?', 'en' => 'What day is it today?'],
                ['it' => 'Oggi', 'en' => 'Today'],
                ['it' => 'Ieri', 'en' => 'Yesterday'],
                ['it' => 'Domani', 'en' => 'Tomorrow'],
                ['it' => 'Questa settimana', 'en' => 'This week'],
                ['it' => 'La prossima settimana', 'en' => 'Next week'],
                ['it' => 'Questo mese', 'en' => 'This month'],
                ['it' => 'L\'anno prossimo', 'en' => 'Next year'],
                ['it' => 'Che data è oggi?', 'en' => 'What\'s the date today?'],
                ['it' => 'Oggi è lunedì', 'en' => 'Today is Monday'],
                ['it' => 'A che ora?', 'en' => 'At what time?'],
                ['it' => 'Alle 8', 'en' => 'At 8 o\'clock'],
                ['it' => 'Tra 10 minuti', 'en' => 'In 10 minutes'],
                ['it' => 'Tra un\'ora', 'en' => 'In an hour'],
                ['it' => 'Adesso', 'en' => 'Now'],
                ['it' => 'Più tardi', 'en' => 'Later'],
                ['it' => 'Presto', 'en' => 'Soon'],
                ['it' => 'Tardi', 'en' => 'Late'],
                ['it' => 'In anticipo', 'en' => 'Early'],
            ],

            'Numeri e contare' => [
                ['it' => 'Uno', 'en' => 'One'],
                ['it' => 'Due', 'en' => 'Two'],
                ['it' => 'Tre', 'en' => 'Three'],
                ['it' => 'Quattro', 'en' => 'Four'],
                ['it' => 'Cinque', 'en' => 'Five'],
                ['it' => 'Sei', 'en' => 'Six'],
                ['it' => 'Sette', 'en' => 'Seven'],
                ['it' => 'Otto', 'en' => 'Eight'],
                ['it' => 'Nove', 'en' => 'Nine'],
                ['it' => 'Dieci', 'en' => 'Ten'],
                ['it' => 'Undici', 'en' => 'Eleven'],
                ['it' => 'Dodici', 'en' => 'Twelve'],
                ['it' => 'Tredici', 'en' => 'Thirteen'],
                ['it' => 'Quattordici', 'en' => 'Fourteen'],
                ['it' => 'Quindici', 'en' => 'Fifteen'],
                ['it' => 'Venti', 'en' => 'Twenty'],
                ['it' => 'Trenta', 'en' => 'Thirty'],
                ['it' => 'Cinquanta', 'en' => 'Fifty'],
                ['it' => 'Cento', 'en' => 'One hundred'],
                ['it' => 'Mille', 'en' => 'One thousand'],
                ['it' => 'Primo', 'en' => 'First'],
                ['it' => 'Secondo', 'en' => 'Second'],
                ['it' => 'Terzo', 'en' => 'Third'],
                ['it' => 'Metà', 'en' => 'Half'],
                ['it' => 'Doppio', 'en' => 'Double'],
            ],

            'Famiglia' => [
                ['it' => 'Madre', 'en' => 'Mother'],
                ['it' => 'Padre', 'en' => 'Father'],
                ['it' => 'Genitori', 'en' => 'Parents'],
                ['it' => 'Sorella', 'en' => 'Sister'],
                ['it' => 'Fratello', 'en' => 'Brother'],
                ['it' => 'Figlio', 'en' => 'Son'],
                ['it' => 'Figlia', 'en' => 'Daughter'],
                ['it' => 'Marito', 'en' => 'Husband'],
                ['it' => 'Moglie', 'en' => 'Wife'],
                ['it' => 'Nonno', 'en' => 'Grandfather'],
                ['it' => 'Nonna', 'en' => 'Grandmother'],
                ['it' => 'Zio', 'en' => 'Uncle'],
                ['it' => 'Zia', 'en' => 'Aunt'],
                ['it' => 'Cugino', 'en' => 'Cousin'],
                ['it' => 'Nipote', 'en' => 'Grandchild / Nephew / Niece'],
                ['it' => 'Sono sposato', 'en' => 'I\'m married'],
                ['it' => 'Sono single', 'en' => 'I\'m single'],
                ['it' => 'Ho un figlio', 'en' => 'I have a son'],
                ['it' => 'Ho una figlia', 'en' => 'I have a daughter'],
                ['it' => 'Quanti anni hai?', 'en' => 'How old are you?'],
                ['it' => 'Ho 30 anni', 'en' => 'I\'m 30 years old'],
                ['it' => 'È mio fratello', 'en' => 'He is my brother'],
                ['it' => 'È mia sorella', 'en' => 'She is my sister'],
                ['it' => 'Questa è la mia famiglia', 'en' => 'This is my family'],
                ['it' => 'Vivo con la mia famiglia', 'en' => 'I live with my family'],
            ],

            'Complimentarsi' => [
                ['it' => 'Ben fatto!', 'en' => 'Well done!'],
                ['it' => 'Ottimo lavoro!', 'en' => 'Great job!'],
                ['it' => 'Bravissimo!', 'en' => 'Good for you!'],
                ['it' => 'Mi piace', 'en' => 'I like it'],
                ['it' => 'Mi piace molto', 'en' => 'I really like it'],
                ['it' => 'È fantastico', 'en' => 'It\'s awesome'],
                ['it' => 'È bellissimo', 'en' => 'It\'s beautiful'],
                ['it' => 'Sei gentile', 'en' => 'You\'re kind'],
                ['it' => 'Sei simpatico', 'en' => 'You\'re nice'],
                ['it' => 'Sei intelligente', 'en' => 'You\'re smart'],
                ['it' => 'Che bel vestito!', 'en' => 'Nice dress!'],
                ['it' => 'Che bella idea!', 'en' => 'That\'s a great idea!'],
                ['it' => 'Ottima scelta', 'en' => 'Good choice'],
                ['it' => 'Mi hai aiutato molto', 'en' => 'You helped me a lot'],
                ['it' => 'Grazie di tutto', 'en' => 'Thanks for everything'],
                ['it' => 'Apprezzo molto', 'en' => 'I really appreciate it'],
                ['it' => 'Complimenti!', 'en' => 'Congratulations!'],
                ['it' => 'Sono fiero di te', 'en' => 'I\'m proud of you'],
                ['it' => 'Sei fantastico', 'en' => 'You\'re amazing'],
                ['it' => 'Ottimo!', 'en' => 'Excellent!'],
                ['it' => 'Perfetto!', 'en' => 'Perfect!'],
            ],

            'Rispondere a una buona notizia' => [
                ['it' => 'Davvero?', 'en' => 'Really?'],
                ['it' => 'Fantastico!', 'en' => 'That\'s great!'],
                ['it' => 'Che bello!', 'en' => 'That\'s wonderful!'],
                ['it' => 'Ottima notizia!', 'en' => 'That\'s good news!'],
                ['it' => 'Sono felice per te', 'en' => 'I\'m happy for you'],
                ['it' => 'Complimenti!', 'en' => 'Congratulations!'],
                ['it' => 'Ben fatto!', 'en' => 'Well done!'],
                ['it' => 'Te lo meriti', 'en' => 'You deserve it'],
                ['it' => 'Che figata!', 'en' => 'That\'s awesome!'],
                ['it' => 'Non ci posso credere!', 'en' => 'I can\'t believe it!'],
                ['it' => 'Sono contentissimo', 'en' => 'I\'m so happy'],
                ['it' => 'Mi fa piacere', 'en' => 'I\'m glad to hear that'],
                ['it' => 'Wow!', 'en' => 'Wow!'],
                ['it' => 'Incredibile!', 'en' => 'Amazing!'],
                ['it' => 'Evviva!', 'en' => 'Hooray!'],
                ['it' => 'Che sorpresa!', 'en' => 'What a surprise!'],
                ['it' => 'Ottimo!', 'en' => 'Awesome!'],
                ['it' => 'Perfetto!', 'en' => 'Perfect!'],
                ['it' => 'Che successo!', 'en' => 'What a success!'],
                ['it' => 'Che bello sentirlo!', 'en' => 'That\'s so nice to hear!'],
                ['it' => 'Mi hai fatto sorridere', 'en' => 'You made me smile'],
            ],

            'Rispondere alle cattive notizie' => [
                ['it' => 'Mi dispiace', 'en' => 'I\'m sorry'],
                ['it' => 'Che peccato', 'en' => 'That\'s a pity'],
                ['it' => 'Oh no!', 'en' => 'Oh no!'],
                ['it' => 'È terribile', 'en' => 'That\'s terrible'],
                ['it' => 'Come posso aiutarti?', 'en' => 'How can I help you?'],
                ['it' => 'Sono qui per te', 'en' => 'I\'m here for you'],
                ['it' => 'Capisco', 'en' => 'I understand'],
                ['it' => 'Dev\'essere difficile', 'en' => 'That must be hard'],
                ['it' => 'Coraggio', 'en' => 'Hang in there'],
                ['it' => 'Andrà tutto bene', 'en' => 'Everything will be okay'],
                ['it' => 'Fammi sapere', 'en' => 'Let me know'],
                ['it' => 'Mi dispiace sentirlo', 'en' => 'I\'m sorry to hear that'],
                ['it' => 'Che sfortuna', 'en' => 'What bad luck'],
                ['it' => 'Non è giusto', 'en' => 'That\'s not fair'],
                ['it' => 'Spero che tu stia meglio', 'en' => 'I hope you feel better'],
                ['it' => 'Ti mando un abbraccio', 'en' => 'I\'m sending you a hug'],
                ['it' => 'Se posso fare qualcosa, dimmelo', 'en' => 'If I can do anything, tell me'],
            ],

            'Sport e hobby' => [
                ['it' => 'Mi piace lo sport', 'en' => 'I like sports'],
                ['it' => 'Qual è il tuo sport preferito?', 'en' => 'What\'s your favorite sport?'],
                ['it' => 'Gioco a calcio', 'en' => 'I play football / soccer'],
                ['it' => 'Vado a correre', 'en' => 'I go running'],
                ['it' => 'Vado in palestra', 'en' => 'I go to the gym'],
                ['it' => 'Mi piace nuotare', 'en' => 'I like swimming'],
                ['it' => 'Mi piace camminare', 'en' => 'I like walking'],
                ['it' => 'Mi piace leggere', 'en' => 'I like reading'],
                ['it' => 'Mi piace ascoltare musica', 'en' => 'I like listening to music'],
                ['it' => 'Mi piace cucinare', 'en' => 'I like cooking'],
                ['it' => 'Suono uno strumento', 'en' => 'I play an instrument'],
                ['it' => 'Suono la chitarra', 'en' => 'I play the guitar'],
                ['it' => 'Guardo film', 'en' => 'I watch movies'],
                ['it' => 'Hai un hobby?', 'en' => 'Do you have a hobby?'],
                ['it' => 'Nel tempo libero...', 'en' => 'In my free time...'],
                ['it' => 'Mi piace viaggiare', 'en' => 'I like traveling'],
                ['it' => 'Mi piace fotografare', 'en' => 'I like taking photos'],
                ['it' => 'Preferisco stare a casa', 'en' => 'I prefer staying at home'],
                ['it' => 'È divertente', 'en' => 'It\'s fun'],
                ['it' => 'È interessante', 'en' => 'It\'s interesting'],
                ['it' => 'Non ho tempo', 'en' => 'I don\'t have time'],
                ['it' => 'Che ne pensi?', 'en' => 'What do you think?'],
                ['it' => 'Andiamo!', 'en' => 'Let\'s go!'],
                ['it' => 'Mi alleno', 'en' => 'I work out'],
                ['it' => 'Facciamo una partita', 'en' => 'Let\'s play a game'],
            ],
        ];

        // ---------------------------------------------
        // Helpers (dedup espressioni + cache contesti)
        // ---------------------------------------------
        $contesti = [];
        $exprCache = [];

        $getOrCreateEspressione = function (Lingua $lingua, string $testo, ?string $info) use (&$exprCache, $manager): Espressione {
            $testo = trim($testo);
            $info = $info !== null ? trim($info) : null;

            $key = $lingua->getDescrizione() . '|' . mb_strtolower($testo) . '|' . mb_strtolower((string)$info);

            if (isset($exprCache[$key])) {
                return $exprCache[$key];
            }

            $e = (new Espressione())
                ->setLingua($lingua)
                ->setTesto($testo)
                ->setInfo($info)
                ->setCorretta(true);

            $manager->persist($e);
            $exprCache[$key] = $e;

            return $e;
        };

        foreach ($dataset as $nomeContesto => $items) {
            $contesto = (new Contesto())->setDescrizione($nomeContesto);
            $manager->persist($contesto);
            $contesti[$nomeContesto] = $contesto;

            foreach ($items as $row) {
                $itText = (string)($row['it'] ?? '');
                $itInfo = $row['it_info'] ?? null;
                $enText = (string)($row['en'] ?? '');

                $exprIt = $getOrCreateEspressione($it, $itText, $itInfo);
                $exprEn = $getOrCreateEspressione($en, $enText, null);

                // IT -> EN
                $fraseItEn = (new Frase())
                    ->setContesto($contesto)
                    ->setDirezione($dirItEn)
                    ->setLivello($base)
                    ->setEspressione($exprIt);
                $manager->persist($fraseItEn);

                $tradItEn = (new Traduzione())
                    ->setFrase($fraseItEn)
                    ->setEspressione($exprEn);
                $manager->persist($tradItEn);

                // EN -> IT (pronto per futuro)
                $fraseEnIt = (new Frase())
                    ->setContesto($contesto)
                    ->setDirezione($dirEnIt)
                    ->setLivello($base)
                    ->setEspressione($exprEn);
                $manager->persist($fraseEnIt);

                $tradEnIt = (new Traduzione())
                    ->setFrase($fraseEnIt)
                    ->setEspressione($exprIt);
                $manager->persist($tradEnIt);
            }
        }

        $manager->flush();
    }
}
