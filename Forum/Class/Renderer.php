<?php

class Renderer {
    private $htmlGenerator;
    private $orm;

    function __construct(ORM $orm)
    {
        $this->htmlGenerator = new HTMLGenerator();
        $this->orm = $orm;
    }

    function render (String $templateName): Void
    {
        $template = file_get_contents(__DIR__ . '/../Templates/' . $templateName . '.html');
        switch ($templateName) {
            case 'homepage':
                $topics = $this->orm->getTopics();
                $html = $this->htmlGenerator->topicList($topics);
                $template = str_replace('{{TOPICS}}', $html, $template);
                break;

            case 'topic':
                $topic = $this->orm->getTopic($_GET['name']);
                $posts = $this->orm->getPosts($topic);
                $html = $this->htmlGenerator->topic($posts);
                $template = str_replace('{{POSTS}}', $html, $template);
                break;
            case 'post':
                $post = $this->orm->getPost($_GET['id']);
                
                $post->setTopic($this->orm->getTopicById($post->getTopic()));
                $post->setAuthor($this->orm->getUserById($post->getAuthor()));
                $html = $this->htmlGenerator->post($post);
                $template = str_replace('{{TOPICNAME}}', $post->getTopic()->getName(), $template);
                $template = str_replace('{{POST}}', $html, $template);
                break;
            default:
                break;
        }
        echo $template;
    }
}