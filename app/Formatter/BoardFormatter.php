<?php

namespace Kanboard\Formatter;

use Kanboard\Core\Filter\FormatterInterface;
use Kanboard\Model\TaskModel;

/**
 * Board Formatter
 *
 * @package formatter
 * @author  Frederic Guillot
 */
class BoardFormatter extends BaseFormatter implements FormatterInterface
{
    /**
     * Project id
     *
     * @access protected
     * @var integer
     */
    protected $projectId;

    /**
     * Set ProjectId
     *
     * @access public
     * @param  integer $projectId
     * @return $this
     */
    public function withProjectId($projectId)
    {
        $this->projectId = $projectId;
        return $this;
    }

    /**
     * Apply formatter
     *
     * @access public
     * @return array
     */
    public function format()
    {
        $swimlanes = $this->swimlaneModel->getSwimlanes($this->projectId);
        $columns = $this->columnModel->getAll($this->projectId);
        $tasks = $this->query
            ->eq(TaskModel::TABLE.'.project_id', $this->projectId)
            ->asc(TaskModel::TABLE.'.position')
            ->findAll();

        $task_ids = array_column($tasks, 'id');
        $tags = $this->taskTagModel->getTagsByTasks($task_ids);

        if (empty($swimlanes) || empty($columns)) {
            return array();
        }

        return BoardSwimlaneFormatter::getInstance($this->container)
            ->withSwimlanes($swimlanes)
            ->withColumns($columns)
            ->withTasks($tasks)
            ->withTags($tags)
            ->format();
    }
}
