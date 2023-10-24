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
    public function votAction(Request $request, $id): Response
    {
        // Create or load a vote (you can modify this part as needed)
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
        var_dump($list);


        return $this->render('@ProductBundle/list_votes.html.twig', [
            'votes' => $votes,
        ]);
    }
}
