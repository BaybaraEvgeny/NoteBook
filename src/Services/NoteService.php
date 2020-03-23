<?php

namespace App\Services;


use App\Entity\Note;
use App\Repository\NoteRepository;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class NoteService
 * @package App\Services
 */
final class NoteService
{
    private $noteRepository;

    public function __construct(NoteRepository $noteRepository)
    {
        $this->noteRepository = $noteRepository;
    }

    public function getNote($id): array
    {
        $note = $this->noteRepository->findById($id);

        if (!$note) {
            throw new NotFoundHttpException('Invalid id!');
        }
        $data = [
            'id' => $note->getId(),
            'title' => $note->getTitle(),
            'text' => $note->getText(),
        ];

        return $data;
    }

    public function getAllNotes(): array
    {
        $notes = $this->noteRepository->findAllNotes();
        $data = [];

        foreach ($notes as $note) {
            $data[] = [
                'id' => $note->getId(),
                'title' => $note->getTitle(),
                'text' => $note->getText(),
            ];
        }

        return $data;
    }

    public function getAllNotesByPage($page): array
    {
        $totalRows = count($this->noteRepository->findAllNotes());

        if (!isset($_GET['amount'])) {
            $notesPerPage = 5;
        } else {
            if (!is_numeric($_GET['amount']) || $_GET['amount'] > $totalRows || $_GET['amount'] <= 0) {
                $notesPerPage = 5;
            } else {
                $notesPerPage = $_GET['amount'];
            }
        }

        $totalPages = (int)ceil($totalRows / $notesPerPage);

        if (!is_numeric($page) || $page > $totalPages || $page <= 0) {
            throw new NotFoundHttpException('Invalid parameters!');
        }

        try {
            $notes = $this->noteRepository->getByPage($page, $notesPerPage);
        } catch (\Exception $e) {
            throw new NotFoundHttpException($e->getMessage());
        }

        $data = [];

        foreach ($notes as $note) {
            $data[] = [
                'id' => $note->getId(),
                'title' => $note->getTitle(),
                'text' => $note->getText(),
            ];
        }

        return $data;
    }

    public function addNote($data): void
    {
        $title = isset($data['title']) ? $data['title'] : '';
        $text = isset($data['text']) ? $data['text'] : '';

        if (empty($title) || empty($text)) {
            throw new NotFoundHttpException('Expecting mandatory parameters!');
        }

        $this->noteRepository->saveNote($title, $text);
    }

    public function updateNote($id, $data): array
    {
        $note = $this->noteRepository->findById($id);

        if (!$note) {
            throw new NotFoundHttpException('Invalid id!');
        }

        $title = isset($data['title']) ? $data['title'] : '';
        $text = isset($data['text']) ? $data['text'] : '';

        if (empty($title) || empty($text)) {
            throw new NotFoundHttpException('Expecting mandatory parameters!');
        }

        $note->setTitle($title);
        $note->setText($text);

        $updatedNote = $this->noteRepository->updateNote($note);

        return $updatedNote->toArray();
    }

    public function deleteNote($id): void
    {
        $note = $this->noteRepository->findById($id);

        if (!$note) {
            throw new NotFoundHttpException('Invalid id!');
        }

        $this->noteRepository->removeNote($note);
    }

}
