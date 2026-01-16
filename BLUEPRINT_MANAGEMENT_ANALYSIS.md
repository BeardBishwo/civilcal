# Blueprint Management System - Analysis & Solution

## 📍 Current Issue

The blueprints displayed at `/blueprint` (Architect's Studio) are **HARDCODED** in the controller:

**Location**: `app/Controllers/Quiz/TerminologyController.php` (Lines 27-39)

```php
$blueprints = [
    [
        'id' => 'beam_structure',
        'title' => 'Structural Beam Layout',
        'difficulty' => 1,
        'reward' => 50,
        'image' => '/themes/basic/assets/img/blueprints/beam_preview.svg'
    ],
    [
        'id' => 'concrete_slab',
        'title' => 'Reinforced Concrete Slab',
        'difficulty' => 2,
        'reward' => 100,
        'image' => '/themes/basic/assets/img/blueprints/slab_preview.svg'
    ]
];
```

## 🎯 Current Architecture

### Admin Panel (CONTROLLED ✓)
- **Route**: `/admin/quiz/blueprints`
- **Controller**: `Admin\Quiz\BlueprintController`
- **Service**: `ExamBlueprintService`
- **Features**: Create, Edit, Update, Delete blueprints
- **Database**: Stores exam templates/blueprints

### Frontend Display (HARDCODED ✗)
- **Route**: `/blueprint`
- **Controller**: `Quiz\TerminologyController@index()`
- **Data**: Hardcoded array (not dynamic)
- **Database**: NOT using exam blueprint data

## 🔧 Solution

There are 2 blueprints/exam systems:

1. **Exam Blueprint System** (Admin-managed)
   - For creating exam templates
   - Stores in database
   - Used for exam generation

2. **Terminology/Blueprint Builder** (Hardcoded)
   - For matching games
   - Currently hardcoded
   - Used for learning via matching

---

## 📊 Database Tables

### `blueprints` (Main blueprints table)
```sql
- id
- title
- description
- level (difficulty)
- total_questions
- total_marks
- duration_minutes
- negative_marking_rate
- wildcard_percentage
- is_active
- created_by
- created_at
- updated_at
```

### `blueprint_reveals` (User progress)
```sql
- id
- user_id
- blueprint_id
- revealed_percentage
- created_at
- updated_at
```

---

## ✅ How to Make It Database-Driven

### Option 1: Use Existing Admin Blueprints (Recommended)

Modify `TerminologyController@index()`:

```php
public function index()
{
    // Get active blueprints from database
    $blueprintService = new ExamBlueprintService();
    $blueprints = $blueprintService->getAllBlueprints();
    
    // Filter for terminology/matching games
    $blueprints = array_filter($blueprints, function($b) {
        return $b['is_active'] == 1;
    });
    
    $progress = $this->revealModel->getUserProgress($_SESSION['user_id']);
    $progressMap = [];
    foreach($progress as $p) {
        $progressMap[$p['blueprint_id']] = $p['revealed_percentage'];
    }
    
    $this->view->render('quiz/games/blueprint_list', [
        'page_title' => 'Architect' . "'" . 's Studio',
        'blueprints' => $blueprints,
        'progress' => $progressMap
    ]);
}
```

### Option 2: Create Separate Table for Terminology Blueprints

Create a new table specifically for matching games with more flexible fields.

---

## 🎮 Current Workflow

### For Admin:
1. Go to `/admin/quiz/blueprints`
2. Create blueprint with rules and questions
3. Blueprints are stored in database

### For Users (Currently):
1. Go to `/blueprint`
2. See only 2 hardcoded blueprints
3. **Cannot see blueprints created in admin panel**

### What Should Happen:
1. Admin creates blueprints in `/admin/quiz/blueprints`
2. They automatically appear on `/blueprint` for users
3. No hardcoding needed
4. Easy to add/remove/modify via admin panel

---

## 📋 Admin Access

**Admin Panel URL**: `http://localhost/Bishwo_Calculator/admin/quiz/blueprints`

**Functions Available**:
- ✓ List all blueprints
- ✓ Create new blueprint
- ✓ Edit existing blueprint
- ✓ Delete blueprint
- ✓ Activate/Deactivate
- ✓ Set difficulty, rewards, duration
- ✓ Generate questions from rules

---

## 🚀 Recommendation

**Make TerminologyController pull from the ExamBlueprintService** instead of using hardcoded data. This way:

1. Everything is managed from admin panel
2. No code changes needed to add blueprints
3. Consistent data across the system
4. Easy to update/modify/delete
5. Can add more blueprints anytime

**Files to Modify**:
- `app/Controllers/Quiz/TerminologyController.php` - Remove hardcoded array
- `app/Controllers/Quiz/TerminologyController.php` - Use ExamBlueprintService

---

## 📂 Files Involved

```
Admin Panel (Database-Driven):
├── app/Controllers/Admin/Quiz/BlueprintController.php
├── app/Services/ExamBlueprintService.php
└── themes/admin/views/quiz/blueprints/

Frontend Display (Hardcoded):
├── app/Controllers/Quiz/TerminologyController.php  ← NEEDS FIXING
└── themes/default/views/quiz/games/blueprint_list.php
```

---

## 💡 Want Me To Fix This?

I can modify the `TerminologyController` to pull blueprints from the database instead of hardcoding them. This will make everything manageable from the admin panel.

Just say "yes, fix it" and I'll implement the change!