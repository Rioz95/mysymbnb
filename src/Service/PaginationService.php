<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;

class PaginationService
{
    private $entityClass;
    private $limit = 10;
    private $currentPage = 1;
    private $manager;
    private $twig;
    private $route;
    private $templatePath;

    public function __construct(EntityManagerInterface $manager, Environment $twig, RequestStack $request, $templatePath)
    {
        $this->route = $request->getCurrentRequest()->attributes->get('_route');

        $this->manager = $manager;
        $this->twig = $twig;
        $this->templatePath = $templatePath;
    }

    public function setTemplatePath($templatePath)
    {
        $this->templatePath = $templatePath;
        return $this;
    }

    public function getTemplatePath()
    {
        return $this->templatePath;
    }

    public function setRoute($route)
    {
        $this->route = $route;
        return $this;
    }

    public function getRoute()
    {
        return $this->route;
    }

    public function display()
    {
        $this->twig->display($this->templatePath, [
            'page' => $this->currentPage,
            'pages' => $this->getPages(),
            'route' => $this->route,
        ]);
    }

    public function getPages()
    {
        if (empty($this->entityClass)) {
            throw new \Exception("Vous n'avez pas spécifié lentité sur laquelle nous devons paginer ! Utiliser la méthode setEntityClass() de votre objet de paginationErvice!");
        }
        // Total Lines Table
        $repo = $this->manager->getRepository($this->entityClass);
        $total = count($repo->findAll());

        // Return Int Divided & Ceil
        return ceil($total / $this->limit);
    }

    public function getData()
    {
        if (empty($this->entityClass)) {
            throw new \Exception("Vous n'avez pas spécifié lentité sur laquelle nous devons paginer ! Utiliser la méthode setEntityClass() de votre objet de paginationErvice!");
        }
        // Calcul Offset
        $offset = $this->currentPage * $this->limit - $this->limit;
        //Get Elements with Repo
        $repo = $this->manager->getRepository($this->entityClass);
        $data = $repo->findBy([], [], $this->limit, $offset);
        // Return Elements
        return $data;
    }

    public function setPage($currentPage)
    {
        $this->currentPage = $currentPage;
        return $this;
    }
    public function getPage()
    {
        return $this->currentPage;
    }



    public function setLimit($limit)
    {
        $this->limit = $limit;
        return $this;
    }
    public function getLimit()
    {
        return $this->limit;
    }



    public function setEnityClass($entityClass)
    {
        $this->entityClass = $entityClass;
        return $this;
    }

    public function getEntityClass()
    {
        return $this->entityClass;
    }
}
