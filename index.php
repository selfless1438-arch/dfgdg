<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Shared To-Do List</title>
  <style>
    * {
      box-sizing: border-box;
      font-family: Poppins, sans-serif;
    }

    body {
      background: #eef2ff;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .todo-container {
      background: #fff;
      width: 400px;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    h2 {
      text-align: center;
      color: #333;
    }

    .input-group {
      display: flex;
      margin-top: 15px;
    }

    input {
      flex: 1;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 8px 0 0 8px;
      outline: none;
    }

    button {
      padding: 10px 15px;
      border: none;
      background: #4CAF50;
      color: #fff;
      border-radius: 0 8px 8px 0;
      cursor: pointer;
      font-weight: 600;
    }

    ul {
      list-style: none;
      margin-top: 20px;
      padding: 0;
    }

    li {
      background: #f5f6ff;
      padding: 10px;
      border-radius: 6px;
      margin-bottom: 8px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    li.completed {
      text-decoration: line-through;
      opacity: 0.6;
    }

    .delete-btn {
      background: #f44336;
      color: #fff;
      border: none;
      border-radius: 6px;
      padding: 5px 8px;
      cursor: pointer;
    }
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
    import { getFirestore, collection, addDoc, deleteDoc, doc, updateDoc, onSnapshot, orderBy, query }
      from "https://www.gstatic.com/firebasejs/12.10.0/firebase-firestore.js";

    // 🔥 Your Firebase config
    const firebaseConfig = {
      apiKey: "AIzaSyBzIxDGwchnYLNO-SXSTFHCXYYWrrL-xlI",
      authDomain: "ypdfg-535c7.firebaseapp.com",
      projectId: "ypdfg-535c7",
      storageBucket: "ypdfg-535c7.firebasestorage.app",
      messagingSenderId: "510197628874",
      appId: "1:510197628874:web:0caa07654b7f6c411a1933"
    };

    // Initialize Firebase
    const app = initializeApp(firebaseConfig);
    const db = getFirestore(app);

    const taskInput = document.getElementById('taskInput');
    const taskList = document.getElementById('taskList');
    const addBtn = document.getElementById('addBtn');
    const tasksRef = collection(db, 'tasks');

    // Add task
    addBtn.addEventListener('click', async () => {
      const text = taskInput.value.trim();
      if (!text) return alert("Enter a task first!");
      await addDoc(tasksRef, { text, completed: false, createdAt: Date.now() });
      taskInput.value = '';
    });

    // Render tasks live
    const q = query(tasksRef, orderBy('createdAt'));
    onSnapshot(q, snapshot => {
      taskList.innerHTML = '';
      snapshot.forEach(docSnap => {
        const data = docSnap.data();
        const li = document.createElement('li');
        li.className = data.completed ? 'completed' : '';
        li.innerHTML = `
        <span style="flex:1;cursor:pointer" data-id="${docSnap.id}">${data.text}</span>
        <button class="delete-btn" data-id="${docSnap.id}">X</button>
      `;
        taskList.appendChild(li);
      });
    });

    // Event delegation for toggling & deleting
    taskList.addEventListener('click', async e => {
      const id = e.target.dataset.id;
      if (!id) return;

      if (e.target.tagName === 'SPAN') {
        // Toggle completed
        const taskDoc = doc(db, 'tasks', id);
        const isCompleted = e.target.parentElement.classList.contains('completed');
        await updateDoc(taskDoc, { completed: !isCompleted });
      } else if (e.target.classList.contains('delete-btn')) {
        // Delete
        await deleteDoc(doc(db, 'tasks', id));
      }
    });
  </script>

</body>

</html>
