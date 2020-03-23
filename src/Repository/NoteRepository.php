<?php

namespace App\Repository;

use App\Entity\Note;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method Note|null find($id, $lockMode = null, $lockVersion = null)
 * @method Note|null findOneBy(array $criteria, array $orderBy = null)
 * @method Note[]    findAll()
 * @method Note[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NoteRepository extends ServiceEntityRepository
{
    private $manager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $manager)
    {
        parent::__construct($registry, Note::class);
        $this->manager = $manager;

    }

    public function findById($id)
    {
        $note = $this->find($id);
        if (!$note || $note->isDeleted()) {
            return null;
        } else {
            return $note;
        }
    }

    public function findAllNotes()
    {
        $notes = $this->findAll();
        $notesFiltered = [];
        foreach ($notes as $note) {
            if (!$note->isDeleted()) {
                $notesFiltered[] = $note;
            }
        }
        return $notesFiltered;
    }

    public function saveNote($title, $text)
    {
        $newNote = new Note();

        $newNote
            ->setTitle($title)
            ->setText($text);

        $this->manager->persist($newNote);
        $this->manager->flush();
    }

    public function updateNote(Note $note): Note
    {
        $this->manager->persist($note);
        $this->manager->flush();

        return $note;
    }

    public function removeNote(Note $note)
    {
        $this->manager->remove($note);
        $this->manager->flush();
    }

    public function getByPage($page, $notesPerPage)
    {
        $offset = ($page - 1) * $notesPerPage;
        return $this->createQueryBuilder('n')
            ->andWhere('n.deletedAt is null')
            ->setFirstResult($offset)
            ->setMaxResults($notesPerPage)
            ->getQuery()
            ->getResult();
    }

}
