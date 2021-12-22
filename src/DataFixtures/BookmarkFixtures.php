<?php

namespace App\DataFixtures;

use App\Entity\Bookmark\BookmarkImage;
use App\Entity\Bookmark\BookmarkVideo;
use App\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BookmarkFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // Data extracted using random url crawled with : https://oscarotero.com/embed/demo/
        $data = [
            (new BookmarkImage('https://www.flickr.com/photos/136969028@N04/50969336517/in/photolist-2kH3LJB-2kJu64X-2kDZb16-2kGjUh6-2kEPZZ2-2kFVL3W-2kDB7Dk-2kDJnEJ-2kJMVwZ-2kCwh5B-2kHNVia-2kGR5UY-2kFmtuL-2kC1VGV-2kGVKEg-2kDJ3DU-2kJpGMQ-2kENdLB-2kH3Te5-2kFh8Kk-2kJgPTo-2kEe5He-2kG17x6-2kFHzAy-2kJhgNA-2kBhiAa-2kJuJNj-2kFmg9y-2kHXZLb-2kHBCCk-2kBDLQe-2kESqJc-2kDVuNU-2kEyRNH-2kEob7Z-2kHwAVH-2kBmMJv-2kGF5fa-2kHZm3G-2kFefSM-2kFjLTV-2kGuoDi-2kJgzws-2kGUeU4-2kGuh5b-2kDugrq-2kFoV4T-2kDpC7x-2kCULfq-2kF9Q3y/'))
                ->setUrl('https://live.staticflickr.com/65535/50969336517_175eb7270c_b.jpg')
                ->setTitle('	People ...')
                ->setAuthor('Lato-Pictures')
                ->setHeight(556)
                ->setWidth(1024)
                ->addTag(Tag::createFromName('bridge'))
                ->addTag(Tag::createFromName('river'))
                ->addTag(Tag::createFromName('netherlands'))
                ->addTag(Tag::createFromName('Holland')),
            (new BookmarkVideo('https://vimeo.com/520991555'))
                ->setUrl('https://vimeo.com/520991555')
                ->setTitle('Global Women - Career Limiting')
                ->setAuthor('Anna Mantzaris')
                ->setHeight(640)
                ->setWidth(360)
                ->setDuration(60)
                ->addTag(Tag::createFromName('video')),
        ];

        foreach ($data as $entity) {
            $manager->persist($entity);
        }

        $manager->flush();
    }
}
