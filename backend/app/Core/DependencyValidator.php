<?php

namespace App\Core;

use App\Core\Contracts\ModuleInterface;

class DependencyValidator
{
    /**
     * Check if a module can be safely disabled without breaking other active modules.
     *
     * @param string $moduleId
     * @param array<string, ModuleInterface>|ModuleInterface[] $allModules
     * @param string[] $enabledModuleIds
     * @return array{can_disable: bool, blocking_dependents: array<string>, reason: ?string}
     */
    public function canDisable(string $moduleId, array $allModules, array $enabledModuleIds): array
    {
        $normalizedModules = $this->normalizeModules($allModules);
        $blockingDependents = [];

        foreach ($enabledModuleIds as $enabledId) {
            if ($enabledId === $moduleId) {
                continue;
            }

            if (isset($normalizedModules[$enabledId])) {
                $dependencies = $normalizedModules[$enabledId]->getDependencies();
                if (in_array($moduleId, $dependencies, true)) {
                    $blockingDependents[] = $enabledId;
                }
            }
        }

        $blockingDependents = array_values(array_unique($blockingDependents));

        if (!empty($blockingDependents)) {
            return [
                'can_disable' => false,
                'blocking_dependents' => $blockingDependents,
                'reason' => sprintf(
                    "Cannot disable module '%s' because active module(s) depend on it: %s",
                    $moduleId,
                    implode(', ', $blockingDependents)
                ),
            ];
        }

        return [
            'can_disable' => true,
            'blocking_dependents' => [],
            'reason' => null,
        ];
    }

    /**
     * Check if a module can be safely enabled.
     *
     * @param string $moduleId
     * @param array<string, ModuleInterface>|ModuleInterface[] $allModules
     * @param string[] $enabledModuleIds
     * @return array{can_enable: bool, missing_dependencies: array<string>, reason: ?string}
     */
    public function canEnable(string $moduleId, array $allModules, array $enabledModuleIds): array
    {
        $normalizedModules = $this->normalizeModules($allModules);

        if (!isset($normalizedModules[$moduleId])) {
            return [
                'can_enable' => false,
                'missing_dependencies' => [],
                'reason' => sprintf("Module '%s' is not registered.", $moduleId),
            ];
        }

        $targetModule = $normalizedModules[$moduleId];
        $dependencies = $targetModule->getDependencies();
        $missingDependencies = [];

        foreach ($dependencies as $depId) {
            if (!isset($normalizedModules[$depId]) || !in_array($depId, $enabledModuleIds, true)) {
                $missingDependencies[] = $depId;
            }
        }

        $missingDependencies = array_values(array_unique($missingDependencies));

        if (!empty($missingDependencies)) {
            return [
                'can_enable' => false,
                'missing_dependencies' => $missingDependencies,
                'reason' => sprintf(
                    "Cannot enable module '%s' because required module(s) are missing or disabled: %s",
                    $moduleId,
                    implode(', ', $missingDependencies)
                ),
            ];
        }

        // Check if enabling this would create any circular dependency
        $simulatedEnabled = array_unique(array_merge($enabledModuleIds, [$moduleId]));
        $simulatedGraph = [];
        foreach ($simulatedEnabled as $id) {
            if (isset($normalizedModules[$id])) {
                $simulatedGraph[$id] = $normalizedModules[$id]->getDependencies();
            }
        }

        $cycles = $this->detectCyclesInAdjacencyList($simulatedGraph);
        if (!empty($cycles)) {
            return [
                'can_enable' => false,
                'missing_dependencies' => [],
                'reason' => sprintf("Cannot enable module '%s' due to circular dependency: %s", $moduleId, implode(' -> ', $cycles[0])),
            ];
        }

        return [
            'can_enable' => true,
            'missing_dependencies' => [],
            'reason' => null,
        ];
    }

    /**
     * Detect all cycles in a set of modules.
     *
     * @param array<string, ModuleInterface>|ModuleInterface[]|array<string, array<string>> $modules
     * @return array<array<string>> List of detected cycles as arrays of node IDs
     */
    public function detectCycles(array $modules): array
    {
        $adj = [];
        if (empty($modules)) {
            return [];
        }

        // Determine format: already adjacency list or ModuleInterface objects
        $first = reset($modules);
        if ($first instanceof ModuleInterface) {
            foreach ($modules as $module) {
                $adj[$module->getId()] = $module->getDependencies();
            }
        } elseif (is_array($first)) {
            $adj = $modules;
        } else {
            foreach ($modules as $k => $v) {
                if ($v instanceof ModuleInterface) {
                    $adj[$v->getId()] = $v->getDependencies();
                } else {
                    $adj[$k] = (array) $v;
                }
            }
        }

        return $this->detectCyclesInAdjacencyList($adj);
    }

    /**
     * Check if a set of modules has any circular dependency.
     *
     * @param array<string, ModuleInterface>|ModuleInterface[]|array<string, array<string>> $modules
     * @return bool
     */
    public function hasCycles(array $modules): bool
    {
        return !empty($this->detectCycles($modules));
    }

    /**
     * Perform topological sort to get execution/boot order of modules.
     *
     * @param array<string, ModuleInterface>|ModuleInterface[] $modules
     * @return string[] Array of module IDs in order of resolution (dependencies first)
     * @throws \RuntimeException If a cycle is detected
     */
    public function getTopologicalOrder(array $modules): array
    {
        $normalized = $this->normalizeModules($modules);
        $adj = [];
        foreach ($normalized as $id => $module) {
            $adj[$id] = array_filter($module->getDependencies(), fn($d) => isset($normalized[$d]));
        }

        $cycles = $this->detectCyclesInAdjacencyList($adj);
        if (!empty($cycles)) {
            throw new \RuntimeException('Circular dependency detected: ' . implode(' -> ', $cycles[0]));
        }

        $visited = [];
        $order = [];

        $visit = function ($node) use (&$visit, &$visited, &$order, $adj) {
            if (isset($visited[$node])) {
                return;
            }
            $visited[$node] = true;

            foreach ($adj[$node] ?? [] as $dep) {
                $visit($dep);
            }

            $order[] = $node;
        };

        foreach (array_keys($adj) as $node) {
            if (!isset($visited[$node])) {
                $visit($node);
            }
        }

        return $order;
    }

    /**
     * Detect cycles using depth-first search on an adjacency list.
     *
     * @param array<string, array<string>> $adj
     * @return array<array<string>>
     */
    protected function detectCyclesInAdjacencyList(array $adj): array
    {
        $visited = [];
        $recStack = [];
        $cycles = [];
        $path = [];

        $dfs = function ($node) use (&$dfs, &$visited, &$recStack, &$cycles, &$path, $adj) {
            $visited[$node] = true;
            $recStack[$node] = true;
            $path[] = $node;

            foreach ($adj[$node] ?? [] as $neighbor) {
                if (!isset($adj[$neighbor])) {
                    // Dependent neighbor not in this graph subset, ignore
                    continue;
                }

                if (!isset($visited[$neighbor])) {
                    $dfs($neighbor);
                } elseif (!empty($recStack[$neighbor])) {
                    // Cycle detected! Extract cycle path
                    $startIndex = array_search($neighbor, $path, true);
                    if ($startIndex !== false) {
                        $cycle = array_slice($path, $startIndex);
                        $cycle[] = $neighbor;
                        $cycles[] = $cycle;
                    }
                }
            }

            array_pop($path);
            $recStack[$node] = false;
        };

        foreach (array_keys($adj) as $node) {
            if (!isset($visited[$node])) {
                $dfs($node);
            }
        }

        return $cycles;
    }

    /**
     * Normalize module input to [id => ModuleInterface].
     *
     * @param array<string, ModuleInterface>|ModuleInterface[] $modules
     * @return array<string, ModuleInterface>
     */
    protected function normalizeModules(array $modules): array
    {
        $result = [];
        foreach ($modules as $key => $module) {
            if ($module instanceof ModuleInterface) {
                $result[$module->getId()] = $module;
            } elseif (is_string($key) && is_object($module)) {
                $result[$key] = $module;
            }
        }
        return $result;
    }
}
