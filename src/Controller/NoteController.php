<?php

namespace App\Controller;

use App\Repository\NoteRepository;
use App\Services\NoteService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class NoteController extends AbstractController
{
    /**
     * @var NoteService
     */
    private $noteService;

    /**
     * @var NoteRepository
     */
    private $noteRepository;

    public function __construct(NoteRepository $noteRepository, NoteService $noteService)
    {
        $this->noteRepository = $noteRepository;
        $this->noteService = $noteService;
    }

    /**
     * @Route("/notes", name="add_note", methods={"POST"})
     */
    public function add(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $this->noteService->addNote($data);

        return new JsonResponse(['status' => 'Note created!'], Response::HTTP_CREATED);
    }

    /**
     * @Route("/notes/{id}", name="get_one_note", methods={"GET"})
     */
    public function get($id): JsonResponse
    {
        $data = $this->noteService->getNote($id);

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /** Get params: page, amount
     * @Route("/notes", name="get_all_notes", methods={"GET"})
     */
    public function getAll(): JsonResponse
    {
        if (isset($_GET['page'])) {
            $data = $this->noteService->getAllNotesByPage($_GET['page']);
        } else {
            $data = $this->noteService->getAllNotes();
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @Route("/notes/{id}", name="update_note", methods={"PUT"})
     */
    public function update($id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $note = $this->noteService->updateNote($id, $data);

        return new JsonResponse($note, Response::HTTP_OK);
    }

    /**
     * @Route("/notes/{id}", name="delete_note", methods={"DELETE"})
     */
    public function delete($id): JsonResponse
    {
        $this->noteService->deleteNote($id);

        return new JsonResponse(['status' => 'Note deleted'], Response::HTTP_OK);
    }
}
