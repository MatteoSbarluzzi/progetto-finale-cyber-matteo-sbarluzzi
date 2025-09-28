<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Tag;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Services\HttpService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    protected $httpService;

    public function __construct(HttpService $httpService)
    {
        $this->httpService = $httpService;
    }

    // Dashboard admin con richieste ruoli + dati finanziari
    public function dashboard()
    {
        $this->authorize('manageAdminArea', User::class);   
        $this->authorize('viewFinancialData', User::class); 

        $adminRequests   = User::whereNull('is_admin')->get();
        $revisorRequests = User::whereNull('is_revisor')->get();
        $writerRequests  = User::whereNull('is_writer')->get();

        // Inizializza sempre, anche se la richiesta fallisce
        $financialData = [];

        try {
            // Effettua la richiesta HTTP verso FinancialApp
            $response = $this->httpService->getRequest('http://internal.finance:8001/user-data.php');

            if (empty($response)) {
                throw new Exception('La risposta dalla richiesta HTTP è vuota.');
            }

            $decoded = json_decode($response, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Errore nella decodifica del JSON: ' . json_last_error_msg());
            }

            $financialData = $decoded;
        } catch (Exception $e) {
            // Log dell'errore invece di echo (così non rompe la view)
            Log::error('Errore in AdminController@dashboard: ' . $e->getMessage());
        }

        return view('admin.dashboard', compact('adminRequests', 'revisorRequests', 'writerRequests', 'financialData'));
    }

    // Assegna ruolo admin ad un utente
    public function setAdmin(User $user)
    {
        $this->authorize('assignRole', $user); 

        $user->is_admin = true;
        $user->save();

        // Audit: assegnazione ruolo admin
        Log::channel('audit')->warning('Role change', [
            'action'       => 'setAdmin',
            'target_id'    => $user->id,
            'target_email' => $user->email,
            'performed_by' => Auth::id(),
            'ip'           => request()->ip(),
            'time'         => now()->toIso8601String(),
        ]);

        return redirect(route('admin.dashboard'))->with('message', "{$user->name} is now administrator");
    }


    // Assegna ruolo Revisor

    public function setRevisor(User $user)
    {
        $this->authorize('assignRole', $user);

        $user->is_revisor = true;
        $user->save();

        // Audit: assegnazione ruolo revisor
        Log::channel('audit')->warning('Role change', [
            'action'       => 'setRevisor',
            'target_id'    => $user->id,
            'target_email' => $user->email,
            'performed_by' => Auth::id(),
            'ip'           => request()->ip(),
            'time'         => now()->toIso8601String(),
        ]);

        return redirect(route('admin.dashboard'))->with('message', "{$user->name} is now revisor");
    }

    // Assegna ruolo writer

    public function setWriter(User $user)
    {
        $this->authorize('assignRole', $user); 
        $user->is_writer = true;
        $user->save();

        // Audit: assegnazione ruolo writer
        Log::channel('audit')->warning('Role change', [
            'action'       => 'setWriter',
            'target_id'    => $user->id,
            'target_email' => $user->email,
            'performed_by' => Auth::id(),
            'ip'           => request()->ip(),
            'time'         => now()->toIso8601String(),
        ]);

        return redirect(route('admin.dashboard'))->with('message', "{$user->name} is now writer");
    }

    // Modifica tag (admin)
    public function editTag(Request $request, Tag $tag)
    {
        $this->authorize('manageAdminArea', User::class); 

        $request->validate([
            'name' => 'required|unique:tags',
        ]);

        $tag->update([
            'name' => strtolower($request->name),
        ]);

        return redirect()->back()->with('message', 'Tag successfully updated');
    }


    // Elimina tag (admin)

    public function deleteTag(Tag $tag)
    {
        $this->authorize('manageAdminArea', User::class); 

        foreach ($tag->articles as $article) {
            $article->tags()->detach($tag);
        }
        $tag->delete();

        return redirect()->back()->with('message', 'Tag successfully deleted');
    }


    // Modifica categoria (admin)

    public function editCategory(Request $request, Category $category)
    {
        $this->authorize('manageAdminArea', User::class); 

        $request->validate([
            'name' => 'required|unique:categories',
        ]);

        $category->update([
            'name' => strtolower($request->name),
        ]);

        return redirect()->back()->with('message', 'Category successfully updated');
    }


    // Elimina categoria (admin)

    public function deleteCategory(Category $category)
    {
        $this->authorize('manageAdminArea', User::class); 

        $category->delete();

        return redirect()->back()->with('message', 'Category successfully deleted');
    }


    // Crea nuova categoria (admin)

    public function storeCategory(Request $request)
    {
        $this->authorize('manageAdminArea', User::class);

        $category = Category::create([
            'name' => strtolower($request->name),
        ]);

        return redirect()->back()->with('message', 'Category successfully created');
    }


    // Crea nuovo tag (admin)

    public function storeTag(Request $request)
    {
        $this->authorize('manageAdminArea', User::class); 

        $tag = Tag::create([
            'name' => strtolower($request->name),
        ]);

        return redirect()->back()->with('message', 'Tag successfully created');
    }
}
