<?php
session_start();
include 'db_connect.php';
include 'audit_log.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header("Location: login.php?error=Please log in as staff");
    exit();
}

$active = 'entry';

$types = $conn->query("SELECT * FROM vehicle_types");
$customers = $conn->query("SELECT user_id, name, email FROM users WHERE role = 'customer'");

// Get ALL slots with their type, so JS can filter client-side without reloading
$all_slots_sql = "SELECT slot_id, slot_number, type_id, status FROM parking_slots ORDER BY slot_number";
$all_slots_result = $conn->query($all_slots_sql);
$all_slots = [];
while ($row = $all_slots_result->fetch_assoc()) {
    $all_slots[] = $row;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Vehicle Entry - ParkEase</title>
    <link rel="stylesheet" href="style.css">
    <script src="theme.js"></script>
</head>
<body>
<div class="app-shell">
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <p class="page-title">Vehicle entry</p>
        <p class="page-subtitle">Check a vehicle into an available slot</p>

        <?php if (isset($_GET['error'])) { ?>
            <p class="error-msg"><?php echo $_GET['error']; ?></p>
        <?php } ?>

        <div style="max-width:500px;">
            <form action="entry_process.php" method="POST" id="entryForm">
                <label>Plate Number</label>
                <input type="text" name="plate_number" placeholder="e.g. DHAKA-1234" required>

                <label>Vehicle Type</label>
                <select name="type_id" id="typeSelect" required>
                    <?php $types->data_seek(0); while ($row = $types->fetch_assoc()) { ?>
                        <option value="<?php echo $row['type_id']; ?>">
                            <?php echo ucfirst($row['type_name']); ?> (Tk <?php echo $row['hourly_rate']; ?>/hr)
                        </option>
                    <?php } ?>
                </select>

                <label>Customer (owner)</label>
                <select name="owner_id" required>
                    <option value="">-- Select customer --</option>
                    <?php while ($row = $customers->fetch_assoc()) { ?>
                        <option value="<?php echo $row['user_id']; ?>">
                            <?php echo $row['name']; ?> (<?php echo $row['email']; ?>)
                        </option>
                    <?php } ?>
                </select>

                <label style="margin-top:18px;">Choose a slot</label>
                <div class="slot-legend">
                    <span><span class="legend-dot" style="background:#e6f4ea;border:1px solid #1a7f5a;"></span>Available</span>
                    <span><span class="legend-dot" style="background:#fdecea;border:1px solid #b3261e;"></span>Occupied</span>
                </div>
                <div class="slot-grid" id="slotGrid"></div>

                <input type="hidden" name="slot_id" id="selectedSlotId" required>

                <button type="submit">🚘 Check In</button>
            </form>
        </div>
    </div>
</div>

<script>
const allSlots = <?php echo json_encode($all_slots); ?>;
const typeSelect = document.getElementById('typeSelect');
const slotGrid = document.getElementById('slotGrid');
const selectedSlotInput = document.getElementById('selectedSlotId');

function renderSlots() {
    const selectedType = typeSelect.value;
    slotGrid.innerHTML = '';
    selectedSlotInput.value = '';

    const slotsForType = allSlots.filter(s => s.type_id == selectedType);

    slotsForType.forEach(slot => {
        const box = document.createElement('div');
        box.className = 'slot-box ' + slot.status;
        box.textContent = slot.slot_number;

        if (slot.status === 'available') {
            box.addEventListener('click', () => {
                document.querySelectorAll('.slot-box.selected').forEach(el => el.classList.remove('selected'));
                box.classList.add('selected');
                selectedSlotInput.value = slot.slot_id;
            });
        }

        slotGrid.appendChild(box);
    });

    if (slotsForType.length === 0) {
        slotGrid.innerHTML = '<p style="font-size:13px;color:#999;">No slots exist for this vehicle type yet.</p>';
    }
}

typeSelect.addEventListener('change', renderSlots);
renderSlots(); // run once on page load

document.getElementById('entryForm').addEventListener('submit', function(e) {
    if (!selectedSlotInput.value) {
        e.preventDefault();
        alert('Please select a parking slot before checking in.');
    }
});
</script>
</body>
</html>