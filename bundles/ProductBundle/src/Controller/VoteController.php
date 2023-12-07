<?php
namespace ProductBundle\Controller;

use ProductBundle\Model\Vote;
use ProductBundle\Model\Vote\Listing;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VoteController extends AbstractController
{
    /**
     * @Route("/vote")
     */
    public function voteAction(Request $request): Response
    {

        $vote = new Vote();
        $vote->setScore(3);
        $vote->setUsername('foobar!' . mt_rand(1, 999));
        $vote->save();

        return $this->render('@ProductBundle/vote.html.twig', [
            'vote' => $vote,
        ]);
    }

    /**
     * @Route("/votes", name="list_votes")
     */
    public function listVotes(): Response
    {
        $list = Listing::create();
        $list->setCondition("score > 2");
        $votes = $list->load();

        return $this->render('@ProductBundle/list_votes.html.twig', [
            'votes' => $votes,
        ]);
    }
}
