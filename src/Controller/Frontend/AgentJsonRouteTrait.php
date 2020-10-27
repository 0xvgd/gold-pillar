<?php

namespace App\Controller\Frontend;

use App\Entity\Person\Agent;
use App\Repository\Person\AgentRepository;
use Symfony\Component\Routing\Annotation\Route;

trait AgentJsonRouteTrait
{
    /**
     * @Route("/agents", name="agents")
     */
    public function agents(AgentRepository $repository)
    {
        $agents = $repository->findAll();

        return $this->json($agents, 200, [], [
            'groups' => ['list'],
        ]);
    }

    /**
     * @Route("/agents/{id}", name="agent_view")
     */
    public function agentView(Agent $agent)
    {
        return $this->json($agent, 200, [], [
            'groups' => ['list'],
        ]);
    }
}
