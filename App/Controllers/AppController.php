<?php

namespace App\Controllers;

//os recursos do miniframework
use MF\Controller\Action;
use MF\Model\Container;

class AppController extends Action {


	public function timeline() {

		$this->validaAutenticacao();
			
		//recuperação dos tweets
		$tweet = Container::getModel('Tweet');

		$tweet->__set('id_usuario', $_SESSION['id']);

		$tweets = $tweet->getAll();

		$this->view->tweets = $tweets;

		$usuario = Container::getModel('Usuario');
		$usuario->__set('id', $_SESSION['id']);

		$this->view->infoUsuario = $usuario->getInfoUser();
		$this->view->totalTweets = $usuario->getTotalTweets();
		$this->view->totalFollowing = $usuario->getTotalFollowing();
		$this->view->totalFollowers = $usuario->getTotalFollowers();

		$this->render('timeline');
		
		
	}

	public function tweet() {

		$this->validaAutenticacao();

		$tweet = Container::getModel('Tweet');

		$tweet->__set('tweet', $_POST['tweet']);
		$tweet->__set('id_usuario', $_SESSION['id']);

		$tweet->salvar();

		header('Location: /timeline');
		
	}

	public function validaAutenticacao() {

		session_start();

		if(!isset($_SESSION['id']) || $_SESSION['id'] == '' || !isset($_SESSION['nome']) || $_SESSION['nome'] == '') {
			header('Location: /?login=erro');
		}	

	}

	public function quemSeguir() {

		$this->validaAutenticacao();

		$pesquisarPor = isset($_GET['pesquisarPor']) ? $_GET['pesquisarPor'] : '';
		
		$usuarios = array();
        
     	if($pesquisarPor != '') {
			
			$usuario = Container::getModel('Usuario');
			$usuario->__set('nome', $pesquisarPor);
			$usuario->__set('id', $_SESSION['id']);
			$usuarios = $usuario->getAll();
		}
        $this->view->usuarios = $usuarios;
        
        $usuario = Container::getModel('Usuario');
		$usuario->__set('id', $_SESSION['id']);

        $this->view->infoUsuario = $usuario->getInfoUser();
		$this->view->totalTweets = $usuario->getTotalTweets();
		$this->view->totalFollowing = $usuario->getTotalFollowing();
		$this->view->totalFollowers = $usuario->getTotalFollowers();

		$this->render('quemSeguir');
	}

	public function acao() {
		$this->validaAutenticacao();
      
		$acao = isset($_GET['acao']) ? $_GET['acao'] : '';
		$id_follower = isset($_GET['id_usuario']) ? $_GET['id_usuario'] : '';

		$usuario = Container::getModel('Usuario');
		$usuario->__set('id', $_SESSION['id']);

		if($acao == 'seguir') {
			$usuario->seguirUsuario($id_follower);

		} else if($acao == 'deixar_de_seguir') {
			$usuario->deixarSeguirUsuario($id_follower);
      }

      header('Location: /quem_seguir');

	}	

	public function remover() {
		
    $this->validaAutenticacao();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        
        $tweetId = isset($_POST['tweet_id']) ? $_POST['tweet_id'] : '';

        if ($tweetId) {
            $tweet = Container::getModel('Tweet');
            $tweet->__set('id', $tweetId);
            $tweet->__set('id_usuario', $_SESSION['id']);

            $tweet->removerTweet();
        }
    }

    
    header('Location: /timeline');
}

}

?>