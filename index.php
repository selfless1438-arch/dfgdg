<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Shared To-Do List</title>
  <style>
    * { box-sizing: border-box; font-family: 'Poppins', sans-serif; }
    body {
      margin: 0;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      background: #eef2ff;
    }
    .todo-container {
      background: #fff;
      width: 90%;
      max-width: 400px;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    h2 {
      text-align: center;
      color: #333;
      margin-bottom: 15px;
    }
    .input-group {
      display: flex;
      margin-bottom: 20px;
    }
    input {
      flex: 1;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 8px 0 0 8px;
      outline: none;
      font-size: 16px;
    }
    button {
      padding: 10px 15px;
      border: none;
      background: #4CAF50;
      color: #fff;
      border-radius: 0 8px 8px 0;
      cursor: pointer;
      font-weight: 600;
      transition: background 0.3s;
    }
    button:hover { background: #45a049; }
    ul {
      list-style: none;
      padding: 0;
      margin: 0;
    }
    li {
      background: #f5f6ff;
      padding: 10px;
      border-radius: 6px;
      margin-bottom: 10px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      transition: background 0.2s, opacity 0.2s;
    }
    li.completed { text-decoration: line-through; opacity: 0.6; }
    li span { flex: 1; cursor: pointer; }
    .delete-btn {
      background: #f44336;
      color: #fff;
      border: none;
      border-radius: 6px;
      padding: 5px 8px;
      cursor: pointer;
      transition: background 0.3s;
    }
    .delete-btn:hover { background: #d32f2f; }
  </style>
</head>
<body>

  <div class="todo-container">
    <h2>🌐 Shared To-Do List</h2>
    <div class="input-group">
      <input type="text" id="taskInput" placeholder="Enter a task...">
      <button id="addBtn">Add</button>
    </div>
    <ul id="taskList"></ul>
  </div>

  <script type="module">
    import { initializeApp } from "https://www.gstatic.com/firebasejs/12.10.0/firebase-app.js";
    import {
      getFirestore, collection, addDoc, deleteDoc, doc,
      updateDoc, onSnapshot, orderBy, query, serverTimestamp
    } from "https://www.gstatic.com/firebasejs/12.10.0/firebase-firestore.js";

    // Firebase configuration
    const firebaseConfig = {
      apiKey: "AIzaSyBiTiBOGhjO1x1XWoAw58e8W4BSxA8hg2M",
      authDomain: "shared-todo-49ae1.firebaseapp.com",
      projectId: "shared-todo-49ae1",
      storageBucket: "shared-todo-49ae1.appspot.com",
      messagingSenderId: "1029604377368",
      appId: "1:1029604377368:web:713f5be9cd4a266bc4546d"
    };

    // Initialize Firebase
    const app = initializeApp(firebaseConfig);
    const db = getFirestore(app);
    const tasksRef = collection(db, 'tasks');

    const taskInput = document.getElementById('taskInput');
    const taskList = document.getElementById('taskList');
    const addBtn = document.getElementById('addBtn');

    // Add task
    async function addTask() {
      const text = taskInput.value.trim();
      if (!text) return alert("Enter a task!");
      await addDoc(tasksRef, { text, completed: false, createdAt: serverTimestamp() });
      taskInput.value = '';
    }

    addBtn.addEventListener('click', addTask);
    taskInput.addEventListener('keypress', (e) => { if (e.key === 'Enter') addTask(); });

    // Render tasks live
    const q = query(tasksRef, orderBy('createdAt'));
    onSnapshot(q, snapshot => {
      taskList.innerHTML = '';
      snapshot.forEach(docSnap => {
        const data = docSnap.data();
        const li = document.createElement('li');
        li.className = data.completed ? 'completed' : '';
        li.innerHTML = `
          <span data-id="${docSnap.id}">${data.text}</span>
          <button class="delete-btn" data-id="${docSnap.id}">X</button>
        `;
        taskList.appendChild(li);
      });
    });

    // Toggle complete / Delete
    taskList.addEventListener('click', async e => {
      const id = e.target.dataset.id;
      if (!id) return;
      if (e.target.tagName === 'SPAN') {
        const taskDoc = doc(db, 'tasks', id);
        const completed = e.target.parentElement.classList.contains('completed');
        await updateDoc(taskDoc, { completed: !completed });
      } else if (e.target.classList.contains('delete-btn')) {
        await deleteDoc(doc(db, 'tasks', id));
      }
    });
  </script>
</body>
</html>
