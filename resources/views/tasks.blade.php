<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MERCY'S Task Manager 2026</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen pb-20">
    <div class="max-w-5xl mx-auto px-4 py-8">
        <header class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900">MERCY NJAMBI'S Task Dashboard</h1>
                <!-- <p class="text-slate-500">Managing tasks for {{ date('Y-m-d') }}</p> -->
            </div>
            <div id="loadingStatus" class="hidden text-blue-600 font-medium animate-pulse">Syncing...</div>
        </header>

        <div id="statsSummary" class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-1">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 sticky top-8">
                    <h2 class="text-xl font-bold mb-4 text-slate-800">New Task</h2>
                    <form id="taskForm" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Task Title</label>
                            <input type="text" id="title" class="w-full border border-slate-300 p-2.5 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="e.g. Finish Audit" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Due Date</label>
                            <input type="date" id="due_date" min="{{ date('Y-m-d') }}" class="w-full border border-slate-300 p-2.5 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Priority</label>
                            <select id="priority" class="w-full border border-slate-300 p-2.5 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="low">Low</option>
                                <option value="medium" selected>Medium</option>
                                <option value="high">High</option>
                            </select>
                        </div>
                        <button type="submit" class="w-full bg-blue-600 text-white font-bold py-3 rounded-lg hover:bg-blue-700 transition shadow-sm">
                            Create Task
                        </button>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-slate-800">Active Tasks</h2>
                    <div class="flex gap-2">
                         <select id="filterStatus" onchange="fetchTasks()" class="text-sm border border-slate-300 rounded-md p-1 bg-white">
                            <option value="">All Statuses</option>
                            <option value="pending">Pending</option>
                            <option value="in_progress">In Progress</option>
                            <option value="done">Done</option>
                         </select>
                    </div>
                </div>
                <div id="taskList" class="space-y-4">
                    </div>
            </div>
        </div>
    </div>

    <script>
    const API_URL = '/api/tasks';

    // 1. Initial Load & Fetching 
    async function fetchTasks() {
        toggleLoading(true);
        try {
            const statusFilter = document.getElementById('filterStatus').value;
            const url = statusFilter ? `${API_URL}?status=${statusFilter}` : API_URL;
            
            const response = await fetch(url);
            const tasks = await response.json();
            
            const taskArray = Array.isArray(tasks) ? tasks : [];
            renderTasks(taskArray);
            fetchReport(taskArray); // Pass the current tasks to the stats function
        } catch (err) {
            console.error(err);
        } finally {
            toggleLoading(false);
        }
    }

    // 2. Fetch Report & Sync Summary Cards 
    async function fetchReport(currentTasks) {
        const today = new Date().toISOString().split('T')[0];
        try {
            // Fetch backend daily report for the 'Completed Today' card 
            const res = await fetch(`${API_URL}/report?date=${today}`);
            const data = await res.json();
            
            const container = document.getElementById('statsSummary');
            
            // Calculate Project-Wide Stats for UI responsiveness
            const highCount = currentTasks.filter(t => t.priority === 'high').length;
            const pendingCount = currentTasks.filter(t => t.status === 'pending').length;
            const progressCount = currentTasks.filter(t => t.status === 'in_progress').length;
            const doneToday = data.summary.high.done + data.summary.medium.done + data.summary.low.done;

            container.innerHTML = `
                <div class="bg-red-50 p-4 rounded-xl border border-red-100">
                    <p class="text-red-600 text-xs font-bold uppercase tracking-wider">Total High Priority</p>
                    <p class="text-2xl font-black text-red-900">${highCount}</p>
                </div>
                <div class="bg-amber-50 p-4 rounded-xl border border-amber-100">
                    <p class="text-amber-600 text-xs font-bold uppercase tracking-wider">Total Pending</p>
                    <p class="text-2xl font-black text-amber-900">${pendingCount}</p>
                </div>
                <div class="bg-blue-50 p-4 rounded-xl border border-blue-100">
                    <p class="text-blue-600 text-xs font-bold uppercase tracking-wider">Total In Progress</p>
                    <p class="text-2xl font-black text-blue-900">${progressCount}</p>
                </div>
                <div class="bg-emerald-50 p-4 rounded-xl border border-emerald-100">
                    <p class="text-emerald-600 text-xs font-bold uppercase tracking-wider">Done (Today Only)</p>
                    <p class="text-2xl font-black text-emerald-900">${doneToday}</p>
                </div>
            `;
        } catch (e) {
            console.log("Daily report syncing...");
        }
    }

    // 3. Render Task Cards 
    function renderTasks(tasks) {
        const list = document.getElementById('taskList');
        if (tasks.length === 0) {
            list.innerHTML = `<div class="text-center py-10 bg-white rounded-xl border border-dashed border-slate-300 text-slate-400">No tasks found. Create one to get started!</div>`;
            return;
        }

        list.innerHTML = tasks.map(task => {
            const isDone = task.status === 'done';
            const isInProgress = task.status === 'in_progress';
            const isPending = task.status === 'pending';

            return `
            <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-start gap-4">
                    <div class="mt-1 w-3 h-3 rounded-full ${getPriorityColor(task.priority)}"></div>
                    <div>
                        <h3 class="font-bold text-slate-800 leading-tight">${task.title}</h3>
                        <div class="flex flex-wrap gap-x-4 gap-y-1 mt-1">
                            <span class="text-xs text-slate-500 font-medium">📅 ${task.due_date}</span>
                            <span class="text-xs font-bold uppercase ${getStatusTextColor(task.status)}">${task.status.replace('_', ' ')}</span>
                            <span class="text-xs text-slate-400">ID: #${task.id}</span>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center gap-2 border-t md:border-t-0 pt-3 md:pt-0">
                    ${isPending ? `<button onclick="updateStatus(${task.id}, 'in_progress')" class="bg-blue-50 text-blue-600 text-xs font-bold px-3 py-2 rounded-lg hover:bg-blue-100">Start Progress</button>` : ''}
                    ${isInProgress ? `<button onclick="updateStatus(${task.id}, 'done')" class="bg-emerald-50 text-emerald-600 text-xs font-bold px-3 py-2 rounded-lg hover:bg-emerald-100">Mark Done</button>` : ''}
                    
                    <button 
                        onclick="deleteTask(${task.id})" 
                        ${!isDone ? 'disabled' : ''} 
                        class="p-2 rounded-lg ${isDone ? 'text-red-500 hover:bg-red-50' : 'text-slate-300 cursor-not-allowed'}"
                        title="${!isDone ? 'Only completed tasks can be deleted' : 'Delete Task'}"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
            </div>
        `}).join('');
    }

    // 4. API Operations 
    document.getElementById('taskForm').onsubmit = async (e) => {
        e.preventDefault();
        const data = {
            title: document.getElementById('title').value,
            due_date: document.getElementById('due_date').value,
            priority: document.getElementById('priority').value
        };

        const res = await fetch(API_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(data)
        });

        if (res.ok) {
            e.target.reset();
            fetchTasks();
        } else {
            const err = await res.json();
            alert(err.error || "Title must be unique for this date [cite: 41]");
        }
    };

    async function updateStatus(id, newStatus) {
        await fetch(`${API_URL}/${id}/status`, {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ status: newStatus })
        });
        fetchTasks();
    }

    async function deleteTask(id) {
        if (confirm("Permanently delete this task?")) {
            const res = await fetch(`${API_URL}/${id}`, { method: 'DELETE' });
            if (!res.ok) alert("Rule Check: Only 'done' tasks can be deleted [cite: 64]");
            fetchTasks();
        }
    }

    // Helpers
    function getPriorityColor(p) { return p === 'high' ? 'bg-red-500' : p === 'medium' ? 'bg-amber-500' : 'bg-emerald-500'; }
    function getStatusTextColor(s) { return s === 'done' ? 'text-emerald-600' : s === 'in_progress' ? 'text-blue-600' : 'text-slate-400'; }
    function toggleLoading(show) { document.getElementById('loadingStatus').classList.toggle('hidden', !show); }
    function showError(msg) { alert(msg); }

    // Initial Load
    fetchTasks();
    </script>
</body>
</html>