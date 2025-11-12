<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();
        
        // Merge session cart with database cart after successful authentication
        $this->cartService->mergeSessionCartWithDatabase();
        
        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     * Enhanced logout with comprehensive error handling and debugging.
     */
    public function destroy(Request $request): RedirectResponse|JsonResponse
    {
        Log::info('Logout request received', [
            'user_id' => Auth::id(),
            'request_method' => $request->method(),
            'ajax' => $request->ajax(),
            'expects_json' => $request->expectsJson(),
            'wants_json' => $request->wantsJson(),
            'user_agent' => $request->userAgent(),
            'ip' => $request->ip()
        ]);

        try {
            // Handle both traditional and AJAX logout requests
            $isAjaxRequest = $request->expectsJson() || $request->wantsJson() || $request->ajax();
            
            // Perform logout
            Auth::guard('web')->logout();
            
            // Invalidate and regenerate session
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            Log::info('User logged out successfully', ['user_id' => Auth::id()]);
            
            if ($isAjaxRequest) {
                return response()->json([
                    'success' => true,
                    'message' => 'Logged out successfully.',
                    'redirect' => url('/')
                ]);
            }
            
            // Traditional logout - redirect to home
            return redirect('/');
            
        } catch (\Exception $e) {
            Log::error('Logout error occurred', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id()
            ]);
            
            if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Logout failed. Please try again.',
                    'error' => config('app.debug') ? $e->getMessage() : 'An error occurred'
                ], 500);
            }
            
            return redirect('/')->with('error', 'Logout failed. Please try again.');
        }
    }
}
