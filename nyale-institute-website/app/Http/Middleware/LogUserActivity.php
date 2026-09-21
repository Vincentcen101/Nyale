<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\UserActivityLog;

class LogUserActivity
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (auth()->check() && $request->method() !== 'GET') {
            $route = $request->route();
            $routeName = $route ? $route->getName() : 'unknown';
            $method = $request->method();

            $action = $this->determineAction($routeName, $method);
            $module = $this->determineModule($routeName);

            UserActivityLog::create([
                'user_id' => auth()->id(),
                'action' => $action,
                'module' => $module,
                'description' => $this->generateDescription($routeName, $request),
                'data' => [
                    'route' => $routeName,
                    'method' => $method,
                    'url' => $request->fullUrl(),
                ],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        return $response;
    }

    private function determineAction($routeName, $method)
    {
        if (str_contains($routeName, 'login')) return 'login';
        if (str_contains($routeName, 'logout')) return 'logout';
        if (str_contains($routeName, 'store') || str_contains($routeName, 'create')) return 'created';
        if (str_contains($routeName, 'update') || str_contains($routeName, 'edit')) return 'updated';
        if (str_contains($routeName, 'delete') || str_contains($routeName, 'destroy')) return 'deleted';
        if (str_contains($routeName, 'export')) return 'exported';
        if (str_contains($routeName, 'suspend')) return 'suspended';
        if (str_contains($routeName, 'activate')) return 'activated';
        if (str_contains($routeName, 'register')) return 'registered';

        return 'updated';
    }

    private function determineModule($routeName)
    {
        if (str_contains($routeName, 'user')) return 'user';
        if (str_contains($routeName, 'work-area') || str_contains($routeName, 'workareas')) return 'work_areas';
        if (str_contains($routeName, 'impact')) return 'impact';
        if (str_contains($routeName, 'knowledge')) return 'knowledge_hub';
        if (str_contains($routeName, 'campaign')) return 'campaigns';
        if (str_contains($routeName, 'post') || str_contains($routeName, 'news')) return 'news';
        if (str_contains($routeName, 'team')) return 'team';
        if (str_contains($routeName, 'get-involved') || str_contains($routeName, 'getinvolved')) return 'get_involved';
        if (str_contains($routeName, 'setting')) return 'settings';
        if (str_contains($routeName, 'auth') || str_contains($routeName, 'login') || str_contains($routeName, 'register')) return 'auth';
        if (str_contains($routeName, 'dashboard')) return 'dashboard';
        if (str_contains($routeName, 'profile')) return 'profile';

        return 'general';
    }

    private function generateDescription($routeName, $request)
    {
        $descriptions = [
            'login' => 'User logged in',
            'logout' => 'User logged out',
            'registered' => 'User registered an account',
            'created' => 'User created a new ' . $this->getResourceName($routeName),
            'updated' => 'User updated ' . $this->getResourceName($routeName),
            'deleted' => 'User deleted ' . $this->getResourceName($routeName),
            'exported' => 'User exported ' . $this->getResourceName($routeName),
            'suspended' => 'User suspended another user',
            'activated' => 'User activated another user',
        ];

        return $descriptions[$this->determineAction($routeName, $request->method())] ?? 'User performed an action';
    }

    private function getResourceName($routeName)
    {
        $parts = explode('.', $routeName);
        return $parts[0] ?? 'resource';
    }
}
