# 📸 Visual Step-by-Step Guide

## What's Wrong?

Your app is like a restaurant:
- **The App** = The restaurant (where customers eat)
- **The Database** = The kitchen (where food is stored)
- **The Problem** = The kitchen isn't set up yet, so the restaurant can't serve food

**Solution:** Set up the kitchen (database) first!

---

## Step 1️⃣: Open Command Prompt

### What to do:
1. Look at your keyboard
2. Find the key that says "Windows" (usually has a Windows logo)
3. Hold it down and press "R" at the same time
4. A small box will appear

### What you'll see:
```
Run
Open: _____________________
[OK]  [Cancel]
```

### What to type:
```
cmd
```

### Then click:
- Click the **OK** button

---

## Step 2️⃣: You're Now in Command Prompt

### What you'll see:
A black window with white text that looks like:
```
C:\Users\YourName>
```

This is your command prompt. It's like a control center where you give instructions to your computer.

---

## Step 3️⃣: Go to Your Project Folder

### What to do:
1. Copy this text:
```
cd c:\laragon\www\Bishwo_Calculator
```

2. Right-click in the black command prompt window
3. Click "Paste"
4. Press Enter

### What you'll see:
```
C:\Users\YourName> cd c:\laragon\www\Bishwo_Calculator
c:\laragon\www\Bishwo_Calculator>
```

✅ **Good!** You're now in the right folder.

---

## Step 4️⃣: Set Up the Database

### What to do:
1. Copy this text:
```
php database/migrate.php
```

2. Paste it in the command prompt
3. Press Enter

### What you'll see:
```
c:\laragon\www\Bishwo_Calculator> php database/migrate.php
Creating tables...
✓ users table created
✓ calculations table created
✓ sessions table created
... (more messages)
Done!
```

✅ **Good!** The database is being set up.

---

## Step 5️⃣: Check if Database Works

### What to do:
1. Copy this text:
```
php tests/database/check_db.php
```

2. Paste it in the command prompt
3. Press Enter

### What you'll see:
```
c:\laragon\www\Bishwo_Calculator> php tests/database/check_db.php
Database connection: SUCCESS
All tables exist: YES
```

✅ **Good!** Database is working!

---

## Step 6️⃣: Restart Laragon

### What to do:
1. Look for **Laragon** on your computer (it has a bird icon 🐦)
2. Click to open it (if not already open)
3. Look for a button that says **STOP** or has a red square
4. Click **STOP**
5. Wait 5 seconds
6. Click **START** or the green play button

### What you'll see:
- First: The Laragon window shows "Stopped"
- Then: After 5 seconds, it shows "Running"

✅ **Good!** Your web server is restarted.

---

## Step 7️⃣: Run the Tests

### What to do:
1. Go back to your command prompt
2. Copy this text:
```
node C:\Users\Bishwo\AppData\Local\npm-cache\_npx\8ddf6bea01b2519d\node_modules\@testsprite\testsprite-mcp\dist\index.js generateCodeAndExecute
```

3. Paste it in the command prompt
4. Press Enter
5. **Wait 5-10 minutes** - it will show progress

### What you'll see:
```
🚀 Starting test execution...
Progress ████████████████████████████████████████ 10/10
✅ Test execution completed
```

### Final Result:
After tests finish, you should see:
```
✅ All 10 tests PASSED
Pass Rate: 100%
```

🎉 **CONGRATULATIONS! Your app is fixed!**

---

## 🆘 Troubleshooting

### Problem 1: Command not found

**What you see:**
```
'php' is not recognized as an internal or external command
```

**What to do:**
- Make sure you're in the right folder
- Type: `cd c:\laragon\www\Bishwo_Calculator`
- Try again

---

### Problem 2: Database connection failed

**What you see:**
```
Error: Could not connect to database
```

**What to do:**
1. Open Laragon
2. Make sure MySQL is running (look for a green indicator)
3. If not running, click START
4. Try the database check again

---

### Problem 3: Tests still fail

**What you see:**
```
❌ Tests Failed
```

**What to do:**
1. Type this to see errors:
```
type debug\logs\error.log
```

2. Look for red error messages
3. Take a screenshot and ask for help

---

## 📋 Checklist

Before you start, make sure you have:

- [ ] Windows computer (or Mac with similar steps)
- [ ] Laragon installed
- [ ] Your project folder at: `c:\laragon\www\Bishwo_Calculator`
- [ ] Command prompt ready to use

---

## 🎯 Quick Summary

| Step | Action | Time |
|------|--------|------|
| 1 | Open Command Prompt | 30 seconds |
| 2 | Go to project folder | 30 seconds |
| 3 | Set up database | 2 minutes |
| 4 | Check database | 30 seconds |
| 5 | Restart Laragon | 1 minute |
| 6 | Run tests | 10 minutes |
| **Total** | **All steps** | **~15 minutes** |

---

## ✨ You Did It!

If you followed all steps and see "All tests PASSED", then:

✅ Your database is set up correctly
✅ Your app is working properly
✅ All 10 tests are passing
✅ You're ready to use your calculator app!

**Great job! 🎉**

---

## 📞 Still Need Help?

1. **Read the error message** - It usually tells you what's wrong
2. **Check the BEGINNER_FIX_GUIDE.md** - More detailed explanations
3. **Check QUICK_START.txt** - Quick reference
4. **Ask someone** - Show them the error message

You've got this! 💪
