<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Media;
use App\Entity\Trick;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // users
        $user = new User();
        $user->setUsername('Demo');
        $user->setEmail('demo@mail.com');
        $user->setStatus('validated');
        $user->setPassword('test');
        $manager->persist($user);

        // tricks
        $trick1 = new Trick();
        $trick1->setUser($user);
        $trick1->setName('mute');
        $trick1->setDate(new \DateTime());
        $trick1->setCategory('grab');
        $trick1->setDescription('A Mute grab is where the front hand grabs the toe edge between the feet. The board is kept flat.');
        $trick1->setSlug('mute');

        $trick2 = new Trick();
        $trick2->setUser($user);
        $trick2->setName('indy');
        $trick2->setDate(new \DateTime());
        $trick2->setCategory('grab');
        $trick2->setDescription('An Indy grab, also known as an Indy air, is an aerial skateboarding, snowboarding and kitesurfing trick during which the rider grabs their back hand on the middle of their board, between their feet, on the side of the board where their toes are pointing, while turning backside.');
        $trick2->setSlug('indy');

        $trick3 = new Trick();
        $trick3->setUser($user);
        $trick3->setName('ollie');
        $trick3->setDate(new \DateTime());
        $trick3->setCategory('jump');
        $trick3->setDescription('An Ollie is probably the first snowboard trick you’ll learn. It’s your introduction to snowboard jumps. To perform an Ollie, you should shift your body weight to your back leg. Jump, making sure to lead with your front leg. Lift your back leg in line with your front.');
        $trick3->setSlug('ollie');

        $trick4 = new Trick();
        $trick4->setUser($user);
        $trick4->setName('Nollie');
        $trick4->setDate(new \DateTime());
        $trick4->setCategory('jump');
        $trick4->setDescription('The Nollie is basically the opposite of an Ollie. When you jump, lead with your back leg. Then, lift your front leg to bring your feet in line with each other. You’ll probably find that you can catch a few inches after just a few tries.');
        $trick4->setSlug('nollie');

        $trick5 = new Trick();
        $trick5->setUser($user);
        $trick5->setName('melon');
        $trick5->setDate(new \DateTime());
        $trick5->setCategory('grab');
        $trick5->setDescription('When you catch some snowboarding air, reach down and grab the heel side of the board between your feet. Congratulations, you’ve done your first Melon!');
        $trick5->setSlug('melon');

        $trick6 = new Trick();
        $trick6->setUser($user);
        $trick6->setName('Nose Grab');
        $trick6->setDate(new \DateTime());
        $trick6->setCategory('grab');
        $trick6->setDescription('If you can do a Melon and Indy, you’re ready for a nose grab. While in the air, reach down to grab the front of your board. Straighten your body and prepare for an easy landing.');
        $trick6->setSlug('nose-grab');

        $trick7 = new Trick();
        $trick7->setUser($user);
        $trick7->setName('50-50');
        $trick7->setDate(new \DateTime());
        $trick7->setCategory('rail');
        $trick7->setDescription('The 50-50 introduces you to snowboard slide tricks. When you approach a rail or box, jump to land on it and ride it until you come off at the other end. Start with short rails until you build the balance you need to ride longer ones.');
        $trick7->setSlug('50-50');

        $trick8 = new Trick();
        $trick8->setUser($user);
        $trick8->setName('Tail Press');
        $trick8->setDate(new \DateTime());
        $trick8->setCategory('press');
        $trick8->setDescription('Practice the tail press on a flat surface where you feel comfortable. Get a little speed going before you lean backward to shift your weight to your back leg. You can lift your front leg to emphasize the bend in your snowboard.');
        $trick8->setSlug('tail-press');

        $trick9 = new Trick();
        $trick9->setUser($user);
        $trick9->setName('Nose press');
        $trick9->setDate(new \DateTime());
        $trick9->setCategory('press');
        $trick9->setDescription('The nose press works just like the tail press. Instead of leaning backward, you shift your weight forward. It can feel a little more intimidating at first, but it requires the same skill. The hardest part is overcoming anxiety to feel comfortable on your snowboard.');
        $trick9->setSlug('nose-press');

        $trick10 = new Trick();
        $trick10->setUser($user);
        $trick10->setName('Frontside 180');
        $trick10->setDate(new \DateTime());
        $trick10->setCategory('rotation');
        $trick10->setDescription('Ready to rotate your snowboard? With a frontside 180, you rotate your body so that your heels lead. For example, if you jump while riding downhill with your left foot forward, you would rotate in a counter counterclockwise motion for a frontside 180. Stop when your rear leg becomes your leading leg.');
        $trick10->setSlug('frontside-180');



        // media
        $media1 = new Media();
        $media1->setTrick($trick1);
        $media1->setType('youtube');
        $media1->setDate(new \DateTime());
        $media1->setContent('https://www.youtube.com/watch?v=M5NTCfdObfs');

        $media2 = new Media();
        $media2->setTrick($trick1);
        $media2->setType('image');
        $media2->setDate(new \DateTime());
        $media2->setContent('814729562ebf2b717b6e5.80481462.png');

        $media3 = new Media();
        $media3->setTrick($trick1);
        $media3->setType('image');
        $media3->setDate(new \DateTime());
        $media3->setContent('709360862ebf2bfe926b1.17857032.gif');

        $trick1->setFeaturedImage($media2);

        // *--------

        $media4 = new Media();
        $media4->setTrick($trick2);
        $media4->setType('image');
        $media4->setDate(new \DateTime());
        $media4->setContent('62445.jpg');
        $trick2->setFeaturedImage($media4);

        $media5 = new Media();
        $media5->setTrick($trick3);
        $media5->setType('image');
        $media5->setDate(new \DateTime());
        $media5->setContent('25975.webp');
        $trick3->setFeaturedImage($media5);

        $media6 = new Media();
        $media6->setTrick($trick4);
        $media6->setType('image');
        $media6->setDate(new \DateTime());
        $media6->setContent('41002.webp');
        $trick4->setFeaturedImage($media6);

        $media7 = new Media();
        $media7->setTrick($trick5);
        $media7->setType('image');
        $media7->setDate(new \DateTime());
        $media7->setContent('6909.jpg');
        $trick5->setFeaturedImage($media7);

        $media8 = new Media();
        $media8->setTrick($trick6);
        $media8->setType('image');
        $media8->setDate(new \DateTime());
        $media8->setContent('65479.jpg');
        $trick6->setFeaturedImage($media8);

        $media9 = new Media();
        $media9->setTrick($trick7);
        $media9->setType('image');
        $media9->setDate(new \DateTime());
        $media9->setContent('17772.jpg');
        
        $media10 = new Media();
        $media10->setTrick($trick7);
        $media10->setType('image');
        $media10->setDate(new \DateTime());
        $media10->setContent('93043.jpg');
        $trick7->setFeaturedImage($media10);

        $media11 = new Media();
        $media11->setTrick($trick8);
        $media11->setType('image');
        $media11->setDate(new \DateTime());
        $media11->setContent('897809762ebf33b6aaea0.73164789.jpg');
        $trick8->setFeaturedImage($media11);

        $media12 = new Media();
        $media12->setTrick($trick9);
        $media12->setType('youtube');
        $media12->setDate(new \DateTime());
        $media12->setContent('https://www.youtube.com/watch?v=Px2YuKQVS_g');
        $media13 = new Media();
        $media13->setTrick($trick9);
        $media13->setType('image');
        $media13->setDate(new \DateTime());
        $media13->setContent('396564762ebf32020fdd7.43185373.png');
        $trick9->setFeaturedImage($media13);

        $media14 = new Media();
        $media14->setTrick($trick10);
        $media14->setType('youtube');
        $media14->setDate(new \DateTime());
        $media14->setContent('https://www.youtube.com/watch?v=GnYAlEt-s00');
        $media15 = new Media();
        $media15->setTrick($trick10);
        $media15->setType('image');
        $media15->setDate(new \DateTime());
        $media15->setContent('846709262ebf2f737e4c6.95990654.webp');
        $media16 = new Media();
        $media16->setTrick($trick10);
        $media16->setType('image');
        $media16->setDate(new \DateTime());
        $media16->setContent('419409862ebf305088027.54510079.gif');
        $trick10->setFeaturedImage($media16);

        for ($i = 1; $i <= 16; $i++) {
            $manager->persist(${"media" . $i});
        }
        for ($i = 1; $i <= 10; $i++) {
            $manager->persist(${"trick" . $i});
        }

        $comment = new Comment();
        $comment->setUser($user);
        $comment->setTrick($trick1);
        $comment->setDate(new \DateTime());
        $comment->setContent('This trick is great !');
        $manager->persist($comment);

        $manager->flush();
    }
}
