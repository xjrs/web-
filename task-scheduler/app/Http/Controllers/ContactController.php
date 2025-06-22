<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserContact;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ContactController extends Controller
{
    /**
     * 获取当前用户的通讯录
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        $contacts = $user-> contactsWithUser()->get()->map(function ($contact) {
            return [
                'id' => $contact->contactUser->id,
                'name' => $contact->display_name,
                'email' => $contact->contact_email,
                'avatar' => $contact->contact_avatar,
                'contact_name' => $contact->contact_name,
                'notes' => $contact->notes,
                'added_at' => $contact->created_at,
                'contact_id' => $contact->id // 通讯录记录ID
            ];
        });
        
        return response()->json([
            'success' => true,
            'data' => $contacts
        ]);
    }
    
    /**
     * 添加联系人
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'contact_user_id' => 'required|exists:users,id',
            'contact_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000'
        ]);
        
        $userId = Auth::id();
        $contactUserId = $request->contact_user_id;
        
        // 检查是否尝试添加自己
        if ($userId == $contactUserId) {
            return response()->json([
                'success' => false,
                'message' => '不能添加自己为联系人'
            ], 400);
        }
        
        // 检查是否已经存在
        $existingContact = UserContact::where('user_id', $userId)
                                    ->where('contact_user_id', $contactUserId)
                                    ->first();
        
        if ($existingContact) {
            return response()->json([
                'success' => false,
                'message' => '该联系人已存在'
            ], 400);
        }
        
        DB::beginTransaction();
        
        try {
            $contact = UserContact::create([
                'user_id' => $userId,
                'contact_user_id' => $contactUserId,
                'contact_name' => $request->contact_name,
                'notes' => $request->notes
            ]);
            
            $contact->load('contactUser');
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $contact->contactUser->id,
                    'name' => $contact->display_name,
                    'email' => $contact->contact_email,
                    'avatar' => $contact->contact_avatar,
                    'contact_name' => $contact->contact_name,
                    'notes' => $contact->notes,
                    'added_at' => $contact->created_at,
                    'contact_id' => $contact->id
                ],
                'message' => '联系人添加成功'
            ], 201);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => '添加联系人失败：' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * 通过邮箱添加联系人
     */
    public function addByEmail(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'contact_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000'
        ]);
        
        $contactUser = User::where('email', $request->email)->first();
        
        if (!$contactUser) {
            return response()->json([
                'success' => false,
                'message' => '该邮箱未注册用户'
            ], 404);
        }
        
        // 调用添加联系人方法
        $addRequest = new Request([
            'contact_user_id' => $contactUser->id,
            'contact_name' => $request->contact_name,
            'notes' => $request->notes
        ]);
        
        return $this->store($addRequest);
    }
    
    /**
     * 更新联系人信息
     */
    public function update(Request $request, UserContact $contact): JsonResponse
    {
        // 检查权限
        if ($contact->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => '您没有权限修改此联系人'
            ], 403);
        }
        
        $request->validate([
            'contact_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000'
        ]);
        
        $contact->update([
            'contact_name' => $request->contact_name,
            'notes' => $request->notes
        ]);
        
        $contact->load('contactUser');
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $contact->contactUser->id,
                'name' => $contact->display_name,
                'email' => $contact->contact_email,
                'avatar' => $contact->contact_avatar,
                'contact_name' => $contact->contact_name,
                'notes' => $contact->notes,
                'added_at' => $contact->created_at,
                'contact_id' => $contact->id
            ],
            'message' => '联系人更新成功'
        ]);
    }
    
    /**
     * 删除联系人
     */
    public function destroy(UserContact $contact): JsonResponse
    {
        // 检查权限
        if ($contact->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => '您没有权限删除此联系人'
            ], 403);
        }
        
        $contact->delete();
        
        return response()->json([
            'success' => true,
            'message' => '联系人删除成功'
        ]);
    }
    
    /**
     * 搜索用户（用于添加联系人）
     */
    public function searchUsers(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'required|string|min:2|max:255'
        ]);
        
        $query = $request->query;
        $userId = Auth::id();
        
        // 搜索用户（排除自己和已添加的联系人）
        $existingContactIds = UserContact::where('user_id', $userId)
                                        ->pluck('contact_user_id')
                                        ->toArray();
        
        $users = User::where('id', '!=', $userId)
                    ->whereNotIn('id', $existingContactIds)
                    ->where(function ($q) use ($query) {
                        $q->where('name', 'like', "%{$query}%")
                          ->orWhere('email', 'like', "%{$query}%");
                    })
                    ->select('id', 'name', 'email', 'avatar')
                    ->limit(10)
                    ->get();
        
        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }
    
    /**
     * 获取推荐联系人
     */
    public function suggestions(): JsonResponse
    {
        $userId = Auth::id();
        
        // 获取已添加的联系人ID
        $existingContactIds = UserContact::where('user_id', $userId)
                                        ->pluck('contact_user_id')
                                        ->toArray();
        
        // 推荐逻辑：
        // 1. 同项目成员
        // 2. 最近注册的用户
        $projectMemberIds = DB::table('user_project_roles')
                             ->whereIn('project_id', function ($query) use ($userId) {
                                 $query->select('project_id')
                                       ->from('user_project_roles')
                                       ->where('user_id', $userId);
                             })
                             ->where('user_id', '!=', $userId)
                             ->whereNotIn('user_id', $existingContactIds)
                             ->pluck('user_id')
                             ->unique()
                             ->toArray();
        
        $suggestions = User::whereIn('id', $projectMemberIds)
                          ->orWhere(function ($query) use ($userId, $existingContactIds) {
                              $query->where('id', '!=', $userId)
                                    ->whereNotIn('id', $existingContactIds)
                                    ->orderBy('created_at', 'desc');
                          })
                          ->select('id', 'name', 'email', 'avatar')
                          ->limit(5)
                          ->get();
        
        return response()->json([
            'success' => true,
            'data' => $suggestions
        ]);
    }
}