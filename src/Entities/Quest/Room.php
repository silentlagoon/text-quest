<?php

namespace App\Entities\Quest;

use Illuminate\Support\Collection;

class Room
{
    protected string $id;
    protected string $name;
    protected string $description;
    protected ?Collection $events;
    protected ?Collection $exits;

    public function __construct(string $id, string $name, string $description)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return Room
     */
    public function setId(string $id): Room
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Room
     */
    public function setName(string $name): Room
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Room
     */
    public function setDescription(string $description): Room
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection|null
     */
    public function getEvents(): ?Collection
    {
        return $this->events;
    }

    /**
     * @param Collection|null $events
     * @return Room
     */
    public function setEvents(?Collection $events): Room
    {
        $this->events = $events;

        return $this;
    }

    /**
     * @return Collection|null
     */
    public function getExits(): ?Collection
    {
        return $this->exits;
    }

    /**
     * @param Collection|null $exits
     * @return Room
     */
    public function setExits(?Collection $exits): Room
    {
        $this->exits = $exits;

        return $this;
    }
}