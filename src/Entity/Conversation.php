<?php

namespace App\Entity;

use App\Repository\ConversationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;


/**
 * @ORM\Entity(repositoryClass=ConversationRepository::class)
 * @ORM\Table(indexes={@Index(name="last_message_id_index", columns={"last_message_id"})})
 */
class Conversation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="Participant", mappedBy="conversation")
     */
    private $participants;

    /**
     * @ORM\OneToOne(targetEntity="Message")
     * @ORM\JoinColumn(name="last_message_id", referencedColumnName="id")
     */
    private $lastMessage;


    /**
     * @ORM\OneToMany(targetEntity="Message", mappedBy="conversation")
     */
    private $messages;

    public function __construct() {
        $this->participants = new ArrayCollection();
        $this->messages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the value of lastMessage
     */ 
    public function getLastMessage()
    {
        return $this->lastMessage;
    }

    /**
     * Set the value of lastMessage
     *
     * @return  self
     */ 
    public function setLastMessage($lastMessage)
    {
        $this->lastMessage = $lastMessage;

        return $this;
    }

    /**
     * Get the value of participants
     * 
     * @return ArrayCollection
     */ 
    public function getParticipants()
    {
        return $this->participants;
    }

    /**
     * add participant to conversation
     *
     * @param   Participant  $participant  
     *
     * @return  self                       
     */
    public function addParticipant(Participant $participant): self
    {
        if (!$this->participants->contains($participant)) {
            $this->participants[] = $participant;
            $participant->setConversation($this);
        }

        return $this;
    }

    /**
     * remove participant from conversation
     *
     * @param   Participant  $participant  
     *
     * @return  self                       
     */
    public function removeParticipant(Participant $participant): self
    {
        if ($this->participants->contains($participant)) {
            $this->participants->removeElement($participant);
            // set the owning side to null (unless already changed)
            if ($participant->getConversation() === $this) {
                $participant->setConversation(null);
            }
        }

        return $this;
    }
    /**
     * Get the value of messages
     * 
     * @return ArrayCollection
     */ 
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * add message to conversation
     *
     * @param   Message  $message  
     *
     * @return  self               
     */
    public function addMessage(Message $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages[] = $message;
            $message->setConversation($this);
        }

        return $this;
    }

    /**
     * remove message from conversation
     *
     * @param   Message  $message  
     *
     * @return  self               
     */
    public function removeMessage(Message $message): self
    {
        if ($this->messages->contains($message)) {
            $this->messages->removeElement($message);
            // set the owning side to null (unless already changed)
            if ($message->getConversation() === $this) {
                $message->setConversation(null);
            }
        }

        return $this;
    }
}
