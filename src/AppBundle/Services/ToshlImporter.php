<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Services;

use Doctrine\ORM\EntityManager;

/**
 * Description of ToshlImporter
 *
 * @author alexseif
 */
class ToshlImporter
{

    protected $em;
    private $csvFile;
    private $data;
    private $categories;
    private $tags;
    private $transactions;
    private $ignoreFirstLine = false;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->data = array();
        $this->transactions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->categories = array();
        $this->tags = array();
    }

    public function parseCsv($file)
    {
        $this->csvFile = $file;
        if (($handle = fopen($this->csvFile->getData(), "r")) !== FALSE) {
            $i = 0;
            while (($data = fgetcsv($handle)) !== FALSE) {
                $i++;
                if ($this->ignoreFirstLine && $i == 1) {
                    continue;
                }

                //Expense
                $data[4] = $this->strToFloat($data[4]);

                //Income
                $data[5] = $this->strToFloat($data[5]);

                if ($data[4] <= 0 && $data[5] <= 0) {
                    dump("Invalid amount at line" . $i);
                    dump($data);
                    continue;
                }
                $this->data[] = $data;
                $this->setCategory($data[2]);
                $rawTags = explode(',', $data[3]);
                foreach ($rawTags as $rawTag) {
                    $tag = $this->setTag($rawTag);
                }
            }
        }
        fclose($handle);
        $this->parseData();
    }

    public function strToFloat($string)
    {
        return (float) abs(str_replace(',', '', $string));
    }

    public function cleanWord($string)
    {
        return ucfirst(trim($string));
    }

    public function setCategory($category)
    {
        $category = $this->cleanWord($category);
        if ("" == $category) {
            dump("Invalid category");
            dump($category);
            return null;
        }

        $cat = $this->em->getRepository('AppBundle:Categories')->findOneBy(array(
            'category' => $category));
        if (!$cat) {
            if (!in_array($category, $this->categories))
                $this->categories[] = $category;
            return null;
        }
        return $cat;
    }

    public function setTag($tag)
    {
        $tag = $this->cleanWord($tag);
        if ("" == $tag) {
            return null;
        }
        $tg = $this->em->getRepository('AppBundle:Tags')->findOneBy(array(
            'tag' => $tag));
        if (!$tg) {
            if (!in_array(strtolower($tag), array_map('strtolower', $this->tags)))
                $this->tags[] = $tag;
            return null;
        }
        return $tg;
    }

    public function parseData()
    {
        foreach ($this->categories as $ct) {
            $category = new \AppBundle\Entity\Categories();
            $category->setCategory($ct);
            $this->persist($category);
        }
        foreach ($this->tags as $tg) {
            $tag = new \AppBundle\Entity\Tags();
            $tag->setTag($tg);
            $this->persist($tag);
        }
        foreach ($this->data as $data) {

            $transaction = new \AppBundle\Entity\Transactions();

            //Date
            $date = \DateTime::createFromFormat('m/d/y', $data[0]);
            $transaction->setDate($date);

            //Account
            $data[1];

            //Category
            $transaction->setCategory($this->setCategory($data[2]));

            //Tags
            $rawTags = explode(',', $data[3]);
            foreach ($rawTags as $rawTag) {
                $tag = $this->setTag($rawTag);
                if ($tag)
                    $transaction->addTag($tag);
            }

            if ($data[4] > 0) {
                //Expense
                $transaction->setAmount($data[4] * 100);
                $transaction->makeExpense();
            } elseif ($data[5] > 0) {
                //Income
                $transaction->setAmount($data[5] * 100);
            }

            //Currency
            $data[6];

            //In EGP
            $data[7];

            //EGP
            $data[8];

            //Description
            $data[9];

            $description = trim($data[9]);
            if ("" != $description)
                $transaction->setDescription($description);

            $this->transactions->add($transaction);
            $this->persist($transaction);
        }
    }

    public function persist($entity)
    {
        $this->em->persist($entity);
        $this->em->flush();
        $this->em->clear();
    }

}
