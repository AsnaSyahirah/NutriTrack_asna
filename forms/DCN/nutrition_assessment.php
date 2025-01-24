<?php
// Start session for storing data
session_start();
require_once 'includes/db.php'; // This file contains your MySQLi database connection

// Ensure user is authenticated and has the correct role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header('Location: ../auth/login.php');
    exit();
}

// Store each test's variables (static tests)
$tests = [
    'acid_base_balance' => [
        'pH_blood' => ['label' => 'pH Blood', 'range' => '7.35 - 7.45', 'unit' => 'pH'],
        'pa_co2' => ['label' => 'Pa CO2', 'range' => '35 - 45 mmHg', 'unit' => 'mmHg'],
        'pa_o2' => ['label' => 'Pa O2', 'range' => '80 - 100 mmHg', 'unit' => 'mmHg'],
        'bicarbonate' => ['label' => 'Bicarbonate', 'range' => '22 - 32 mmol/L', 'unit' => 'mmol/L'],
        'spo2' => ['label' => 'SPO2', 'range' => '92 - 100 %', 'unit' => '%']
    ],
    'electrolyte_renal_profile' => [
        'urea' => ['label' => 'Urea', 'range' => '2.78 - 8.07 mmol/L', 'unit' => 'mmol/L'],
        'sodium' => ['label' => 'Sodium', 'range' => '136 - 145 mmol/L', 'unit' => 'mmol/L'],
        'potassium' => ['label' => 'Potassium', 'range' => '3.5 - 5.1 mmol/L', 'unit' => 'mmol/L'],
        'creatinine' => ['label' => 'Creatinine', 'range' => '62 - 106 mmol/L', 'unit' => 'mmol/L'],
        'phosphorus' => ['label' => 'Phosphorus', 'range' => '0.87 - 1.45 mmol/L', 'unit' => 'mmol/L'],
        'magnesium' => ['label' => 'Magnesium', 'range' => '0.66 - 0.99 mmol/L', 'unit' => 'mmol/L'],
        'calcium' => ['label' => 'Calcium', 'range' => '2.20 - 2.55 mmol/L', 'unit' => 'mmol/L']
    ],
    // Full Blood Count
    'full_blood_count' => [
        'white_blood_cell' => ['label' => 'White Blood Cell', 'range' => '4.0 - 10.0 x10^9/L', 'unit' => 'x10^9/L'],
        'red_blood_cell' => ['label' => 'Red Blood Cell', 'range' => '4.5 - 5.5 x10^12/L', 'unit' => 'x10^12/L'],
        'hemoglobin' => ['label' => 'Hemoglobin', 'range' => '13 - 15 g/dL', 'unit' => 'g/dL'],
        'hematocrit' => ['label' => 'Hematocrit', 'range' => '40.0 - 50.0%', 'unit' => '%']
    ],
    // Blood Sugar Profile
    'blood_sugar_profile' => [
        'fbs' => ['label' => 'Fasting Blood Sugar', 'range' => 'xx mg/dL', 'unit' => 'mg/dL'],
        'rbs' => ['label' => 'Random Blood Sugar', 'range' => '70 - 140 mg/dL', 'unit' => 'mg/dL'],
        '2hpp' => ['label' => '2-Hour Postprandial', 'range' => 'xx mmol/L', 'unit' => 'mmol/L'],
        'hba1c' => ['label' => 'HbA1c', 'range' => 'xx mmol/mol', 'unit' => 'mmol/mol']
    ],
    // Lipid Profile
    'lipid_profile' => [
        'total_cholesterol' => ['label' => 'Total Cholesterol', 'range' => '3.5 - 6.5 mmol/L', 'unit' => 'mmol/L'],
        'tg' => ['label' => 'Triglycerides', 'range' => '1.69 - 2.25 mmol/L', 'unit' => 'mmol/L'],
        'ldl' => ['label' => 'LDL Cholesterol', 'range' => '3.5 - 6.5 mmol/L', 'unit' => 'mmol/L'],
        'hdl' => ['label' => 'HDL Cholesterol', 'range' => '> 0.75 mmol/L (Male) > 0.91 mmol/L (Female)', 'unit' => 'mmol/L']
    ],
    // Liver Profile
    'liver_profile' => [
        'total_protein' => ['label' => 'Total Protein', 'range' => '64.0 - 83.0 g/L', 'unit' => 'g/L'],
        'albumin' => ['label' => 'Albumin', 'range' => '35.0 - 52.0 g/L', 'unit' => 'g/L'],
        'pre_albumin' => ['label' => 'Pre-albumin', 'range' => '15 - 36 mg/dL', 'unit' => 'mg/dL'],
        'tbilirubin' => ['label' => 'Total Bilirubin', 'range' => '0.2 - 1.3 mg/dL', 'unit' => 'mg/dL'],
        'alp' => ['label' => 'ALP', 'range' => '40 - 129 IU/L', 'unit' => 'IU/L'],
        'alt' => ['label' => 'ALT', 'range' => '<= 50 U/L', 'unit' => 'U/L'],
        'amylase' => ['label' => 'Amylase', 'range' => '30 - 110 U/L', 'unit' => 'U/L']
    ],
    // Urine Profile
    'urine_profile' => [
        'urine_color' => ['label' => 'Urine Color', 'range' => 'clear - pale yellow', 'unit' => ''],
        'urine_glucose' => ['label' => 'Urine Glucose', 'range' => 'xx mmol/L', 'unit' => 'mmol/L'],
        'urine_protein' => ['label' => 'Urine Protein', 'range' => 'xx g/L', 'unit' => 'g/L'],
        'urine_ketone' => ['label' => 'Urine Ketone', 'range' => '< 0.1 mmol/L', 'unit' => 'mmol/L']
    ],
    // Inflammatory Profile
    'inflammatory_profile' => [
        'crp' => ['label' => 'C-Reactive Protein', 'range' => '< 5 mg/L', 'unit' => 'mg/L']
    ],
    // Others - Placeholder for adding additional tests
    'others' => [
        'parameter' => ['label' => 'Other Parameter', 'range' => 'xx', 'unit' => '']
    ]
];


// Function to save data in the database 
function saveTestData($user_id, $test_key, $test_data) {
    global $conn;
    
    // Loop through test data and insert into the database
    foreach ($test_data as $param_key => $data) {
        $test_value = $data['value'];
        $range = $data['range'];
        $unit = $data['unit'];
        $test_date = $data['date'];
        
        // Prepare SQL statement to insert test data into the database
        $stmt = $conn->prepare("INSERT INTO tests (user_id, test_key, param_key, test_value, range, unit, test_date) 
                                VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssss", $user_id, $test_key, $param_key, $test_value, $range, $unit, $test_date);
        $stmt->execute();
        $stmt->close();
    }

    return true;
}

// Function to retrieve data from the database
function getPreviousTestData($user_id, $test_key) {
    global $conn;

    $stmt = $conn->prepare("SELECT param_key, test_value, range, unit, test_date FROM tests WHERE user_id = ? AND test_key = ? ORDER BY test_date DESC");
    $stmt->bind_param("is", $user_id, $test_key);
    $stmt->execute();

    $result = $stmt->get_result();
    $previous_data = [];

    while ($row = $result->fetch_assoc()) {
        $previous_data[] = $row;
    }

    $stmt->close();

    return $previous_data;
}

// Handle form submission and save session data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Save general patient information if submitted
    if (isset($_POST['hospital'])) {
        $_SESSION['hospital'] = htmlspecialchars($_POST['hospital']);
        $_SESSION['ward_bed'] = htmlspecialchars($_POST['ward_bed']);
        $_SESSION['date'] = htmlspecialchars($_POST['date']);
        $_SESSION['time'] = htmlspecialchars($_POST['time']);
        $_SESSION['name'] = htmlspecialchars($_POST['name']);
        $_SESSION['height'] = floatval($_POST['height']);
        $_SESSION['weight'] = floatval($_POST['weight']);
    }

    // Save test-specific information based on the form submitted
    foreach ($tests as $test_key => $test) {
        $test_data = []; // to hold the current test's data before insertion

        foreach ($test as $param_key => $param_data) {
            $param_value = $_POST[$param_key] ?? null;
            $param_date = $_POST[$param_key . '_date'] ?? null;
            $param_range = $_POST[$param_key . '_range'] ?? $param_data['range'];

            if ($param_value !== null) {
                // Populate session and prepare test data for insertion
                $_SESSION[$param_key] = htmlspecialchars($param_value);
                $_SESSION[$param_key . '_date'] = htmlspecialchars($param_date);
                $_SESSION[$param_key . '_range'] = htmlspecialchars($param_range);

                // Save the parameter data for the database
                $test_data[$param_key] = [
                    'value' => $param_value,
                    'range' => $param_range,
                    'unit' => $param_data['unit'],
                    'date' => $param_date ?: date("Y-m-d") // default to today's date if no date is submitted
                ];
            }
        }

        // Save data into the database for this test after populating the session data
        if (!empty($test_data)) {
            saveTestData($_SESSION['user_id'], $test_key, $test_data);
        }
    }

    // Move to the next section based on section parameter
    $section = isset($_GET['section']) ? $_GET['section'] : 'A';
    $next_section = getNextSection($section); // Assume this function calculates the next section
    header("Location: nutrition_assessment.php?section=$next_section");
    exit();
}


// Get section to display based on the URL parameter 'section'
$section = isset($_GET['section']) ? $_GET['section'] : 'A';

switch ($section) {
    case 'A':
        displaySectionA();
        break;
    case 'B':
        displaySectionB();
        break;
    case 'C':
        displaySectionC();
        break;
    case 'D':
        displaySectionD();
        break;
    case 'E':
        displaySectionE();
        break;
    default:
        displaySectionA();
        break;
}

// Function to get the next section
function getNextSection($currentSection)
{
    $sections = ['A', 'B', 'C', 'D', 'E'];
    $currentIndex = array_search($currentSection, $sections);
    $nextIndex = ($currentIndex + 1) % count($sections); // Cycle back to A after E
    return $sections[$nextIndex];
}

// Function to display Section A
function displaySectionA()
{
?>
    <form action="nutrition_assessment.php?section=B" method="POST">
        <div class="form-section">
            <h2>Dietetic Care Notes - Section A: Client History</h2>

            <h3>Personal Data</h3>
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo isset($_SESSION['name']) ? $_SESSION['name'] : ''; ?>" required><br>
            
            <label for="literacy">Literacy Factors:</label>
            <select id="literacy" name="literacy">
                <option value="Literate" <?php echo (isset($_SESSION['literacy']) && $_SESSION['literacy'] == 'Literate') ? 'selected' : ''; ?>>Literate</option>
                <option value="Illiterate" <?php echo (isset($_SESSION['literacy']) && $_SESSION['literacy'] == 'Illiterate') ? 'selected' : ''; ?>>Illiterate</option>
                <option value="Low Literacy" <?php echo (isset($_SESSION['literacy']) && $_SESSION['literacy'] == 'Low Literacy') ? 'selected' : ''; ?>>Low Literacy</option>
                <option value="Language Barriers" <?php echo (isset($_SESSION['literacy']) && $_SESSION['literacy'] == 'Language Barriers') ? 'selected' : ''; ?>>Language Barriers</option>
            </select>
            <small>Choose an appropriate literacy status for the patient/client (Literate, Illiterate, Low literacy, Language Barriers).</small><br>

            <label for="age">Age:</label>
            <input type="number" id="age" name="age" value="<?php echo isset($_SESSION['age']) ? $_SESSION['age'] : ''; ?>"><br>

            <label for="role">Role in family:</label>
            <input type="text" id="role" name="role" value="<?php echo isset($_SESSION['role']) ? $_SESSION['role'] : ''; ?>"><br>

            <label for="sex">Sex:</label>
            <select id="sex" name="sex">
                <option value="Male" <?php echo (isset($_SESSION['sex']) && $_SESSION['sex'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                <option value="Female" <?php echo (isset($_SESSION['sex']) && $_SESSION['sex'] == 'Female') ? 'selected' : ''; ?>>Female</option>
            </select><br>

            <label for="tobacco_use">Tobacco use:</label>
            <input type="text" id="tobacco_use" name="tobacco_use" value="<?php echo isset($_SESSION['tobacco_use']) ? $_SESSION['tobacco_use'] : ''; ?>"><br>

            <label for="language">Language:</label>
            <select id="language" name="language">
                <option value="Malay" <?php echo (isset($_SESSION['language']) && $_SESSION['language'] == 'Malay') ? 'selected' : ''; ?>>Malay</option>
                <option value="English" <?php echo (isset($_SESSION['language']) && $_SESSION['language'] == 'English') ? 'selected' : ''; ?>>English</option>
                <option value="Chinese" <?php echo (isset($_SESSION['language']) && $_SESSION['language'] == 'Chinese') ? 'selected' : ''; ?>>Chinese</option>
                <option value="Tamil" <?php echo (isset($_SESSION['language']) && $_SESSION['language'] == 'Tamil') ? 'selected' : ''; ?>>Tamil</option>
                <option value="Others" <?php echo (isset($_SESSION['language']) && $_SESSION['language'] == 'Others') ? 'selected' : ''; ?>>Others</option>
            </select>
            <input type="text" name="other_language" value="<?php echo isset($_SESSION['other_language']) ? $_SESSION['other_language'] : ''; ?>" placeholder="If Others, specify here">
            <small>If 'Others' is selected, please specify the language used.</small><br>

            <label for="education">Education:</label>
            <select id="education" name="education">
                <option value="Primary" <?php echo (isset($_SESSION['education']) && $_SESSION['education'] == 'Primary') ? 'selected' : ''; ?>>Primary</option>
                <option value="Secondary" <?php echo (isset($_SESSION['education']) && $_SESSION['education'] == 'Secondary') ? 'selected' : ''; ?>>Secondary</option>
                <option value="Pre-university" <?php echo (isset($_SESSION['education']) && $_SESSION['education'] == 'Pre-university') ? 'selected' : ''; ?>>Pre-university</option>
                <option value="Diploma" <?php echo (isset($_SESSION['education']) && $_SESSION['education'] == 'Diploma') ? 'selected' : ''; ?>>Diploma</option>
                <option value="Higher Education" <?php echo (isset($_SESSION['education']) && $_SESSION['education'] == 'Higher Education') ? 'selected' : ''; ?>>Higher Education</option>
            </select><br>

            <label for="physical_disability">Physical disability:</label>
            <input type="text" id="physical_disability" name="physical_disability" value="<?php echo isset($_SESSION['physical_disability']) ? $_SESSION['physical_disability'] : ''; ?>"><br>

            <label for="mobility">Mobility:</label>
            <input type="text" id="mobility" name="mobility" value="<?php echo isset($_SESSION['mobility']) ? $_SESSION['mobility'] : ''; ?>"><br>

            <label for="rn">RN:</label>
            <input type="text" id="rn" name="rn" value="<?php echo isset($_SESSION['rn']) ? $_SESSION['rn'] : ''; ?>"><br>

            <label for="others">Others:</label>
            <input type="text" id="others" name="others" value="<?php echo isset($_SESSION['others']) ? $_SESSION['others'] : ''; ?>"><br>

            <h3>Patient/Client/Family Medical/Health History</h3>
            <label for="medical_diagnosis">Medical Diagnosis:</label>
            <textarea id="medical_diagnosis" name="medical_diagnosis"><?php echo isset($_SESSION['medical_diagnosis']) ? $_SESSION['medical_diagnosis'] : ''; ?></textarea><br>
            <small>Provide details of the patient/client's medical diagnosis and history.</small><br>

            <label for="treatments_therapy">Treatments/Therapy:</label>
            <textarea id="treatments_therapy" name="treatments_therapy"><?php echo isset($_SESSION['treatments_therapy']) ? $_SESSION['treatments_therapy'] : ''; ?></textarea><br>
            <small>Include any relevant treatments and therapies (optional).</small><br>

            <label for="family_health_history">Family nutrition-oriented medical/health history:</label>
            <textarea id="family_health_history" name="family_health_history"><?php echo isset($_SESSION['family_health_history']) ? $_SESSION['family_health_history'] : ''; ?></textarea><br>
            <small>Provide details of family nutrition-oriented medical/health history (optional).</small><br>

            <h3>Social History</h3>
            <label for="social_history">Social History:</label>
            <textarea id="social_history" name="social_history"><?php echo isset($_SESSION['social_history']) ? $_SESSION['social_history'] : ''; ?></textarea><br>
            <small>Provide social background relevant to nutrition assessment (optional).</small><br>

            <input type="submit" value="Next (Section B)">
        </div>
    </form>
<?php
}

// Function to display Section B: Anthropometric Measurement with ASEAN BMI Classification
function displaySectionB()
{
?>
    <form action="nutrition_assessment.php?section=C" method="POST">
        <div class="form-section">
            <h2>Dietetic Care Notes - Section B: Anthropometric Measurement</h2>

            <h3>Anthropometric Measurements</h3>

            <label for="date">Date:</label>
            <input type="date" id="date" name="date" value="<?php echo isset($_SESSION['date']) ? $_SESSION['date'] : ''; ?>" required><br>

            <label for="height">Height (cm):</label>
            <input type="number" id="height" name="height" step="0.1" value="<?php echo isset($_SESSION['height']) ? $_SESSION['height'] : ''; ?>" required><br>

            <label for="weight">Weight (kg):</label>
            <input type="number" id="weight" name="weight" step="0.1" value="<?php echo isset($_SESSION['weight']) ? $_SESSION['weight'] : ''; ?>" required><br>

            <label for="bmi">BMI:</label>
            <input type="text" id="bmi" name="bmi" value="<?php echo isset($_SESSION['bmi']) ? $_SESSION['bmi'] : ''; ?>" disabled><br>

            <p id="bmiResult"></p> <!-- Display the calculated BMI here -->
            <p id="classificationResult"></p> <!-- Display BMI classification -->

            <label for="others">Others (Optional):</label>
            <input type="text" id="others" name="others" value=""><br>

            <!-- Button to add more measurements -->
            <button type="button" id="addMeasurement">Add More Measurement</button>

            <h4>Previous Measurements:</h4>
            <div id="previousMeasurements">
                <!-- Previously added measurements will be appended here -->
            </div>

            <!-- Display BMI Chart -->
            <canvas id="bmiChart" width="400" height="200"></canvas>

            <input type="submit" value="Next (Section C)">
        </div>
    </form>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // JavaScript to auto-calculate BMI and classify based on ASEAN classification
        document.getElementById('height').addEventListener('input', calculateBMI);
        document.getElementById('weight').addEventListener('input', calculateBMI);

        // Track previous measurements for the chart
        let heightData = [];
        let weightData = [];
        let bmiData = [];
        let classifications = []; // To hold the classification of each BMI

        // Function to calculate BMI and update graph
        function calculateBMI() {
            var height = parseFloat(document.getElementById('height').value);
            var weight = parseFloat(document.getElementById('weight').value);

            if (height > 0 && weight > 0) {
                var heightInMeters = height / 100; // convert to meters
                var bmi = weight / (heightInMeters * heightInMeters);

                document.getElementById('bmi').value = bmi.toFixed(2); // Update BMI field
                document.getElementById('bmiResult').innerText = "Calculated BMI: " + bmi.toFixed(2); // Display calculated BMI

                // Classify BMI
                var classification = classifyBMI(bmi);
                document.getElementById('classificationResult').innerText = classification; // Display classification result
            }
        }

        // Function to classify BMI based on ASEAN references
        function classifyBMI(bmi) {
            if (bmi < 18.5) {
                return "Classification: Underweight (BMI < 18.5)";
            } else if (bmi >= 18.5 && bmi < 23) {
                return "Classification: Normal (BMI 18.5 - 22.9)";
            } else if (bmi >= 23 && bmi < 27.5) {
                return "Classification: Overweight (BMI 23 - 27.4)";
            } else if (bmi >= 27.5) {
                return "Classification: Obese (BMI >= 27.5)";
            } else {
                return "Invalid BMI value";
            }
        }

        // Add previous measurements dynamically
        document.getElementById('addMeasurement').addEventListener('click', addMeasurement);

        function addMeasurement() {
            var height = document.getElementById('height').value;
            var weight = document.getElementById('weight').value;
            var bmi = document.getElementById('bmi').value;

            if (height && weight && bmi) {
                // Store the current measurements
                heightData.push(height);
                weightData.push(weight);
                bmiData.push(bmi);

                // Classify the current BMI for the graph coloring
                classifications.push(classifyBMI(parseFloat(bmi)));

                // Append the new measurement to the list
                var prevMeasurementDiv = document.createElement('div');
                prevMeasurementDiv.classList.add('measurement-item');
                prevMeasurementDiv.innerHTML = `
                    <p>Height: ${height} cm, Weight: ${weight} kg, BMI: ${bmi} - ${classifications[classifications.length - 1]}</p>
                `;
                document.getElementById('previousMeasurements').appendChild(prevMeasurementDiv);

                // Clear the input fields after saving the measurement
                document.getElementById('height').value = '';
                document.getElementById('weight').value = '';
                document.getElementById('bmi').value = '';
                document.getElementById('bmiResult').innerText = ''; // Reset the result message
                document.getElementById('classificationResult').innerText = ''; // Reset classification message

                // Call the function to update the graph with the new data
                updateBMIChart();
            } else {
                alert("Please enter valid height, weight, and BMI.");
            }
        }

        // Function to update BMI Chart using Chart.js
        function updateBMIChart() {
            var ctx = document.getElementById('bmiChart').getContext('2d');

            // Create or update the Chart.js graph
            if (window.bmiChart) {
                window.bmiChart.destroy(); // Destroy previous chart instance
            }

            window.bmiChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: heightData, // Use height as X-axis labels for reference
                    datasets: [{
                        label: 'BMI Over Time',
                        data: bmiData, // Use BMI data for Y-axis
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: getBMIColor(),
                        fill: false
                    }]
                },
                options: {
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Height (cm)'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'BMI'
                            }
                        }
                    }
                }
            });
        }

        // Function to return a color based on the BMI classification
        function getBMIColor() {
            return bmiData.map(function(bmi) {
                if (bmi < 18.5) return 'rgba(255, 99, 132, 0.2)'; // Underweight (red)
                if (bmi >= 18.5 && bmi < 23) return 'rgba(75, 192, 192, 0.2)'; // Normal (green)
                if (bmi >= 23 && bmi < 27.5) return 'rgba(255, 159, 64, 0.2)'; // Overweight (yellow)
                return 'rgba(153, 102, 255, 0.2)'; // Obese (purple)
            });
        }
    </script>
<?php
}

// Function to display Section C overview (cards for each test type)
function displaySectionC() {
    global $tests;
    ?>
    <form action="" method="POST">
        <div class="form-section">
            <h2>Section C: Biochemical Data & Medical Tests</h2>
            <?php
            foreach ($tests as $test_key => $test) {
                echo "<div class='card' onclick=\"location.href='?section=C&test=$test_key'\">";
                echo "<h3>" . ucwords(str_replace('_', ' ', $test_key)) . "</h3>";
                echo "<p>Click to provide test results for " . ucwords(str_replace('_', ' ', $test_key)) . ".</p></div>";
            }
            ?>
        </div>
    </form>
    <style>
        .form-section {
            padding: 20px;
            display: flex;
            flex-wrap: wrap;
        }
        .card {
            background-color: #f1f1f1;
            width: 300px;
            padding: 15px;
            margin: 10px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            cursor: pointer;
        }
        .card h3 {
            margin-top: 0;
            color: #0056b3;
        }
        .card p {
            font-size: 14px;
            color: #555;
        }
        .card:hover {
            background-color: #e1e1e1;
        }
    </style>
    <?php
}

// Function to display individual test form based on test key and include Chart.js
function displayTestForm($test_key) {
    global $tests;
    $test = $tests[$test_key];
    ?>
    <h3>Enter <?php echo ucwords(str_replace('_', ' ', $test_key)); ?> Test Results</h3>
    <form method="POST">
        <?php
        foreach ($test as $key => $data) {
            // Handle dynamic inputs for test variables
            echo "<label for='$key'>" . $data['label'] . ":</label>";
            echo "<input type='number' step='0.01' name='$key' value='" . (isset($_POST[$key]) ? $_POST[$key] : '') . "' required><br>";
            
            // Display reference range dropdown
            echo "<label for='{$key}_range'>Reference Range:</label>";
            echo "<select name='{$key}_range'>";
            echo "<option value='{$data['range']}' selected>{$data['range']}</option>";
            echo "</select><br>";
            
            // Display unit
            echo "<label for='{$key}_unit'>Unit:</label>";
            echo "<input type='text' value='{$data['unit']}' readonly><br>";

            // Date Input for data entry
            echo "<label for='{$key}_date'>Date:</label>";
            echo "<input type='date' name='{$key}_date' value='" . (isset($_POST[$key . "_date"]) ? $_POST[$key . "_date"] : '') . "' required><br>";
        }
        ?>
        <button type="submit" name="add_<?php echo $test_key; ?>">Add Data</button>
    </form>

    <h4>Previous Measurements for <?php echo ucwords(str_replace('_', ' ', $test_key)); ?>:</h4>
    <?php
    // Retrieve and display previous measurements from the database
    if (isset($_SESSION['user_id'])) {
        $previous_measurements = getPreviousTestData($_SESSION['user_id'], $test_key);
        
        $labels = [];
        $data = [];
        
        foreach ($previous_measurements as $measurement) {
            $labels[] = $measurement['test_date']; 
            $data[] = $measurement['test_value']; // You can use other param keys as well for various data points
            
            echo "<p><strong>Date:</strong> " . $measurement['test_date'] . "</p>";
            echo "<p>{$tests[$test_key][$measurement['param_key']]['label']}: {$measurement['test_value']} {$measurement['unit']}</p>";
            echo "<p>Reference Range: {$measurement['range']}</p>";
            echo "<hr>";
        }

        // Chart.js plot section
        echo "<canvas id='testChart' width='400' height='200'></canvas>";
        echo "<script src='https://cdn.jsdelivr.net/npm/chart.js'></script>";
        echo "<script>
                const ctx = document.getElementById('testChart').getContext('2d');
                const testChart = new Chart(ctx, {
                    type: 'line', // Change to bar or other chart types as needed
                    data: {
                        labels: " . json_encode($labels) . ",
                        datasets: [{
                            label: 'Test Value',
                            data: " . json_encode($data) . ",
                            borderColor: 'rgb(75, 192, 192)',
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            fill: false
                        }]
                    },
                    options: {
                        scales: {
                            x: { type: 'time', time: { unit: 'day' } },
                            y: { beginAtZero: false }
                        }
                    }
                });
              </script>";
    }
}

// Handling POST request to save form data and show previous measurements
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    // Form submission logic: save the data into the database
    if (isset($_POST['add_' . $_GET['test']])) {
        $test_key = $_GET['test'];
        $test_data = [];
        
        foreach ($tests[$test_key] as $param_key => $param_data) {
            $test_data[$param_key] = [
                'value' => $_POST[$param_key], // test parameter value
                'range' => $_POST[$param_key . '_range'], // test parameter range
                'unit' => $param_data['unit'], // unit
                'date' => $_POST[$param_key . '_date'] // date of test
            ];
        }

        // Save to database
        saveTestData($_SESSION['user_id'], $test_key, $test_data);
        echo "<p>Test data has been saved!</p>";
    }
}

// Handle the display of different test forms or Section C
if (isset($_GET['section']) && $_GET['section'] === 'C') {
    if (isset($_GET['test'])) {
        $test = $_GET['test'];

        // Show the respective test form
        if (array_key_exists($test, $tests)) {
            displayTestForm($test);
        } else {
            echo "<p>Invalid Test</p>";
        }
    } else {
        displaySectionC();
    }
}

// Function to display Section D
function displaySectionD()
{
?>
    <form action="nutrition_assessment.php?section=D" method="POST">
        <div class="section">
            <h2>D. Nutrition-focused Physical Findings</h2>

            <!-- Blood Pressure -->
            <div class="card">
                <label>Date: <input type="date" name="bp_date"></label>
                <label>Blood Pressure</label>
                <input type="number" name="systolic" placeholder="Systolic (mmHg)" onchange="updateBloodPressureGraph()">
                <input type="number" name="diastolic" placeholder="Diastolic (mmHg)" onchange="updateBloodPressureGraph()">
                <div id="bp-graph"></div>
            </div>

            <!-- Heart Rate and Respiratory Rate -->
            <div class="card">
                <label>Date: <input type="date" name="heart_date"></label>
                <label>Heart Rate</label>
                <input type="number" name="heart_rate" placeholder="Heart rate (bpm)">
                <label>Respiratory Rate</label>
                <input type="number" name="respiratory_rate" placeholder="Respiratory rate (bpm)">
            </div>

            <!-- Temperature -->
            <div class="card">
                <label>Date: <input type="date" name="temp_date"></label>
                <label>Temperature</label>
                <input type="number" name="temperature" placeholder="Temperature (°C)" onchange="updateTemperatureGraph()">
                <div id="temp-graph"></div>
            </div>

            <!-- Input/Output -->
            <div class="card">
                <label>Date: <input type="date" name="io_date"></label>
                <label>Input/Output</label>
                <input type="number" name="input" placeholder="Input (mL)">
                <input type="number" name="output" placeholder="Output (mL)">
                <label>Output Bowel Specific:</label>
                <input type="text" name="output_bowel" placeholder="Enter details">
            </div>

            <!-- Overall Findings -->
            <br>
            <h4>Overall Findings</h4>
            <div class="columns">
                <div class="column">
                <div class="checkbox-group">
                    <label><input type="checkbox"> Cachexia</label>
                    <label><input type="checkbox"> Lethargy</label>
                    <label><input type="checkbox"> Obese</label>
                    <label><input type="checkbox"> Cushingoid appearance</label>
                    <label><input type="checkbox"> Others: <input type="text" placeholder="Description"></label>
                </div>
                <div class="checkbox-group">
                    <label>Adipose</label>
                    <label><input type="checkbox"> Excess subcutaneous fat</label>
                    <label><input type="checkbox"> Loss subcutaneous fat</label>
                    <label><input type="checkbox"> Central adiposity</label>
                </div>
                <div class="checkbox-group">
                    <label>Bones</label>
                    <label><input type="checkbox"> Bow legs</label>
                    <label><input type="checkbox"> Rickets</label>
                    <label><input type="checkbox"> Scoliosis</label>
                    <label><input type="checkbox"> Others: <input type="text" placeholder="Description"></label>
                </div>
                <div class="checkbox-group">
                    <label>Cardiovascular-pulmonary system</label>
                    <label><input type="checkbox"> Bradycardia</label>
                    <label><input type="checkbox"> Bradypnea</label>
                    <label><input type="checkbox"> Tachypnea</label>
                    <label><input type="checkbox"> Tachycardia</label>
                    <label><input type="checkbox"> Others: <input type="text" placeholder="Description"></label>
                </div>
                <div class="checkbox-group">
                    <label>Digestive system</label>
                    <label><input type="checkbox"> Abdominal bloating/cramping</label>
                    <label><input type="checkbox"> Ascites</label>
                    <label><input type="checkbox"> Constipation/Diarrhea</label>
                    <label><input type="checkbox"> Nausea/Vomiting</label>
                    <label><input type="checkbox"> Others: <input type="text" placeholder="Description"></label>
                </div>
                <div class="checkbox-group">
                    <label>Head</label>
                    <label><input type="checkbox"> Anosmia</label>
                    <label><input type="checkbox"> Bulging fontanelle</label>
                    <label><input type="checkbox"> Headache</label>
                    <label><input type="checkbox"> Macro/microcephaly</label>
                    <label><input type="checkbox"> Others: <input type="text" placeholder="Description"></label>
                </div>
                <div class="checkbox-group">
                    <label>Hand and nails</label>
                    <label><input type="checkbox"> Clubbing of nails</label>
                    <label><input type="checkbox"> Jaundiced sclera</label>
                    <label><input type="checkbox"> Night blindness</label>
                    <label><input type="checkbox"> Sunken eyes</label>
                    <label><input type="checkbox"> Xerophthalmia</label>
                    <label><input type="checkbox"> Others: <input type="text" placeholder="Description"></label>
                </div>
                <div class="checkbox-group">
                    <label>Mouth</label>
                    <label><input type="checkbox"> Ageusia</label>
                    <label><input type="checkbox"> Cleft palate</label>
                    <label><input type="checkbox"> Drooling</label>
                    <label><input type="checkbox"> Gingivitis</label>
                    <label><input type="checkbox"> Poor oral hygiene</label>
                    <label><input type="checkbox"> Others: <input type="text" placeholder="Description"></label>
                </div>
                <div class="checkbox-group">
                    <label>Muscles</label>
                    <label><input type="checkbox"> Muscle atrophy/cramp/pain/weakness</label>
                    <label><input type="checkbox"> Quadriceps muscle wasting</label>
                    <label><input type="checkbox"> Others: <input type="text" placeholder="Description"></label>
                </div>
                <div class="checkbox-group">
                    <label>Neck</label>
                    <label><input type="checkbox"> Goiter</label>
                </div>
                </div>
                <div class="column">
                <div class="checkbox-group">
                    <label>Adipose</label>
                    <label><input type="checkbox"> Specify: <input type="text" placeholder="Description"></label>
                </div>
                <div class="checkbox-group">
                    <label>Extremities</label>
                    <label><input type="checkbox"> Amputated: <input type="text" placeholder="Description"></label>
                    <label><input type="checkbox"> Decrease range of movement: <input type="text" placeholder="Description"></label>
                    <label><input type="checkbox"> Spasticity</label>
                    <label><input type="checkbox"> Hyper/hypotonia</label>
                </div>
                <div class="checkbox-group">
                    <label>Eyes</label>
                    <label><input type="checkbox"> Biotot's spot</label>
                    <label><input type="checkbox"> Night blindness</label>
                    <label><input type="checkbox"> Sunken eyes</label>
                    <label><input type="checkbox"> Xerophthalmia</label>
                    <label><input type="checkbox"> Others: <input type="text" placeholder="Description"></label>
                </div>
                <div class="checkbox-group">
                    <label>Genitourinary system</label>
                    <label><input type="checkbox"> Amenorrhea</label>
                    <label><input type="checkbox"> Anuria</label>
                    <label><input type="checkbox"> Menorrhagia</label>
                    <label><input type="checkbox"> Oliguria/Polyuria</label>
                    <label><input type="checkbox"> Others: <input type="text" placeholder="Description"></label>
                </div>
                <div class="checkbox-group">
                    <label>Hair</label>
                    <label><input type="checkbox"> Alopecin</label>
                    <label><input type="checkbox"> Brittle hair</label>
                    <label><input type="checkbox"> Corkscrew hairs</label>
                    <label><input type="checkbox"> Increase loss of hair</label>
                    <label><input type="checkbox"> Others: <input type="text" placeholder="Description"></label>
                </div>
                <div class="checkbox-group">
                    <label>Nerves, cognition, and feelings</label>
                    <label><input type="checkbox"> Ataxia</label>
                    <label><input type="checkbox"> Delirious/Dementia</label>
                    <label><input type="checkbox"> Dizziness</label>
                    <label><input type="checkbox"> Many seizures a day</label>
                    <label><input type="checkbox"> Numbness</label>
                    <label><input type="checkbox"> Tingling of hand/foot</label>
                    <label><input type="checkbox"> Others: <input type="text" placeholder="Description"></label>
                </div>
                <div class="checkbox-group">
                    <label>Skin</label>
                    <label><input type="checkbox"> Decrease skin turgor</label>
                    <label><input type="checkbox"> Dermatitis/Eczema</label>
                    <label><input type="checkbox"> Hirsutism</label>
                    <label><input type="checkbox"> Jaundice</label>
                    <label><input type="checkbox"> Pressure injury: <input type="text" placeholder="Description"></label>
                    <label><input type="checkbox"> Psoriasis</label>
                    <label><input type="checkbox"> Others: <input type="text" placeholder="Description"></label>
                </div>
                <div class="checkbox-group">
                    <label>Throat & swallowing</label>
                    <label><input type="checkbox"> Choking during swallowing</label>
                    <label><input type="checkbox"> Cough/ Hoarse voice</label>
                    <label><input type="checkbox"> Dysphagia</label>
                    <label><input type="checkbox"> Odynophagia</label>
                    <label><input type="checkbox"> Suck, swallow, breath incoordination</label>
                </div>
                <div class="checkbox-group">
                    <label>Tongue</label>
                    <label><input type="checkbox"> Glossitis</label>
                    <label><input type="checkbox"> Lesion of tongue</label>
                    <label><input type="checkbox"> Macroglossia</label>
                    <label><input type="checkbox"> Strawberry tongue</label>
                    <label><input type="checkbox"> Others: <input type="text" placeholder="Description"></label>
                </div>
            </div>

            <label for="others">Others</label>
            <textarea id="others" name="others" placeholder="Enter additional details here..."></textarea>

            <!-- Submit button -->
            <input type="submit" value="Next (Section E)">
        </div>
    </form>

    <!-- Add script for graph updates -->
    <script>
        function updateBloodPressureGraph() {
            const systolic = document.querySelector('input[name="systolic"]').value;
            const diastolic = document.querySelector('input[name="diastolic"]').value;

            let graphContent = "Blood Pressure: ";
            if (systolic && diastolic) {
                const systolicStatus = systolic >= 90 && systolic <= 120 ? "Normal" : "Abnormal";
                const diastolicStatus = diastolic >= 60 && diastolic <= 80 ? "Normal" : "Abnormal";
                graphContent += `Systolic: ${systolic} (${systolicStatus}), Diastolic: ${diastolic} (${diastolicStatus})`;
            } else {
                graphContent += "Waiting for input...";
            }
            document.getElementById("bp-graph").innerText = graphContent;
        }

        function updateTemperatureGraph() {
            const temperature = document.querySelector('input[name="temperature"]').value;

            let graphContent = "Temperature: ";
            if (temperature) {
                const tempStatus = temperature >= 36.5 && temperature <= 37.5 ? "Normal" : "Abnormal (Possible Fever)";
                graphContent += `${temperature}°C (${tempStatus})`;
            } else {
                graphContent += "Waiting for input...";
            }
            document.getElementById("temp-graph").innerText = graphContent;
        }
    </script>
<?php
}


// Function to display Section E (Final Submission
function displaySectionE() {
?>
    <form action="nutrition_assessment.php?section=E" method="POST">
        <div class="section">
            <h2>E. Dietary Assessment</h2>

            <!-- Select Meal Type -->
            <div class="meal-header">
                <label for="meal-type">Meal Type:</label>
                <select id="meal-type" name="meal_type" onchange="showMealForm()">
                    <option value="">Select Meal Type</option>
                    <option value="breakfast">Breakfast</option>
                    <option value="lunch">Lunch</option>
                    <option value="dinner">Dinner</option>
                </select>
            </div>

            <!-- Container for Meal Forms -->
            <div id="meal-forms-container">
                <!-- Dynamic meal forms will be inserted here -->
            </div>
            <div class="section">
            <h2>E. Food/Nutrient-related History</h2>
            
            <h3>Food and Nutrient Administration:</h3>
            <h4>Diet Order</h4>
            <div class="diet-order">
                <div class="diet-group">
                    <h4>Oral</h4>
                    <label><input type="checkbox" name="diet_oral[]" value="NBM"> NBM</label>
                    <label><input type="checkbox" name="diet_oral[]" value="Clear Fluid"> Clear Fluid</label>
                    <label><input type="checkbox" name="diet_oral[]" value="Nourishing Fluid"> Nourishing Fluid</label>
                    <label><input type="checkbox" name="diet_oral[]" value="Normal Diet"> Normal Diet</label>
                    <label><input type="checkbox" name="diet_oral[]" value="Therapeutic Diet: Description"> Therapeutic Diet</label>
                    <label><input type="checkbox" name="diet_oral[]" value="Modified Texture Diet"> Modified Texture Diet</label>
                </div>
                <div class="diet-group">
                    <h4>Enteral</h4>
                    <label><input type="checkbox" name="diet_enteral[]" value="Nasogastric"> Nasogastric</label>
                    <label><input type="checkbox" name="diet_enteral[]" value="Nasojejunal"> Nasojejunal</label>
                    <label><input type="checkbox" name="diet_enteral[]" value="Nasoduodenal"> Nasoduodenal</label>
                    <label><input type="checkbox" name="diet_enteral[]" value="PEG"> PEG</label>
                    <label><input type="checkbox" name="diet_enteral[]" value="PEJ"> PEJ</label>
                </div>
                <div class="diet-group">
                    <h4>Parenteral</h4>
                    <label><input type="checkbox" name="diet_parenteral[]" value="Partial"> Partial</label>
                    <label><input type="checkbox" name="diet_parenteral[]" value="Total"> Total</label>
                </div>
            </div>
            <br>
            <label for="diet-others">Others:</label>
            <textarea id="diet-others" name="diet_others"></textarea>

            <h3>Diet Experience:</h3>
            <label for="allergy">Food Allergy/Intolerance:</label>
            <textarea id="allergy" name="allergy"></textarea>
            <label for="environment">Eating Environment:</label>
            <textarea id="environment" name="environment"></textarea>
            <label for="fasting">Fasting:</label>
            <textarea id="fasting" name="fasting"></textarea>

            <h3>Medication and Complementary/Alternative Medicine Use:</h3>
            
            <!-- Medications -->
            <h3>Medications</h3>
            <hr>
            <label>Enter Medications</label>
            <div id="medications-container">
                <div class="input-container">
                    <input type="text" name="medications[]" placeholder="Add used medication">
                    <button type="button" onclick="addMedication()">+</button>
                </div>
            </div>

            <br>
            <h3>Complementary/Alternative Medicine</h3> 
            <hr>
            <label>Enter Complementary/Alternative Medicine</label>
            <div id="comp-meds-container">
                <div class="input-container">
                    <input type="text" name="comp_meds[]" placeholder="Add complementary/alternative medication">
                    <button type="button" onclick="addCompMedicine()">+</button>
                </div>
            </div>

            <br>
            <h3>Knowledge/Beliefs/Attitudes:</h3>
            <textarea id="knowledge" name="knowledge"></textarea>

            <h3>Factors Affecting Access to Food and Food/Nutrition-Related Supplies:</h3>
            <textarea id="factors" name="factors"></textarea>

            <h3>Physical Activity and Function:</h3>
            <textarea id="physical-activity" name="physical_activity"></textarea>

            <h3>Nutrition-Related Patient/Client-Centered Measures:</h3>
            <textarea id="patient-measures" name="patient_measures"></textarea>

            <h3>Energy Requirement</h3>
            <hr>
            <label>Activity Level (PAL):</label>
            <select name="activity_level" style="background-color: #ffecec;" onchange="handlePALChange()">
                <option value="1.2">Sedentary (little to no exercise)</option>
                <option value="1.375">Lightly Active (light exercise or sports 1-3 days/week)</option>
                <option value="1.55">Moderately Active (moderate exercise or sports 3-5 days/week)</option>
                <option value="1.725">Very Active (hard exercise or sports 6-7 days a week)</option>
                <option value="1.9">Super Active (very hard exercise or a physically demanding job)</option>
            </select>

            <div id="dynamic-table">
                <!-- BMR Formulas and Calculations will be generated dynamically -->
            </div>

            <h3>Protein Requirement</h3>
            <hr>
            <label>Recommended Protein Intake (g):</label>
            <input type="text" name="protein" style="background-color: #ffecec;" readonly>

            <h3>CHO and Fat Recommendations</h3>
            <hr>
            <label>CHO Recommendation (g):</label>
            <input type="text" name="cho_recommendation" readonly>
            <label>Fat Recommendation (g):</label>
            <input type="text" name="fat_recommendation" readonly>
            <hr>

            <label>Others Recommendation (Eg: Fiber/Purine/Sodium):</label>
            <textarea></textarea>

            <div style="display: flex; justify-content: end;">
                <button class="add-button" type="button">Add</button>
            </div>

            <h3>Summary:</h3>
            <hr>
            <table class="summary-table">
                <thead>
                    <tr>
                        <th>Energy (kcal)</th>
                        <th>Protein (g)</th>
                        <th>CHO (g)</th>
                        <th>Fat (g)</th>
                        <th>Fluid (ml)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input type="text" placeholder="Energy" name="energy"></td>
                        <td><input type="text" placeholder="Protein" name="protein"></td>
                        <td><input type="text" placeholder="CHO" name="cho"></td>
                        <td><input type="text" placeholder="Fat" name="fat"></td>
                        <td><input type="text" placeholder="Fluid" name="fluid"></td>
                    </tr>
                </tbody>
            </table>
            <label>Justification/Comparative Standard:</label>
            <textarea name="justification"></textarea>
            </div>

    </form>

    <script>
        // JavaScript logic to handle dynamic meal form generation
        function showMealForm() {
            const mealType = document.getElementById("meal-type").value;
            if (!mealType) return;

            // Check if the form for this meal type already exists
            const existingForm = document.getElementById(`${mealType}-form`);
            if (existingForm) {
                alert(`${mealType.charAt(0).toUpperCase() + mealType.slice(1)} form already exists.`);
                return;
            }

            // Create meal form container
            const mealForm = document.createElement("div");
            mealForm.id = `${mealType}-form`;
            mealForm.className = "meal-form";
            mealForm.innerHTML = `
                <h3>${mealType.charAt(0).toUpperCase() + mealType.slice(1)}</h3>
                <div class="food-entries-container">
                    <!-- Dynamic food items will be added here -->
                </div>
                <form class="food-entry-container">
                    <label for="foodInput">Enter type of food/drink</label>
                    <input type="text" class="food-input" aria-label="Enter type of food or drink"/>

                    <div class="nutrients-grid">
                        <div>
                            <label>Amount</label>
                            <input type="number" class="nutrient-input" aria-label="Amount"/>
                        </div>
                        <div>
                            <label>Energy (kcal)</label>
                            <input type="number" class="nutrient-input" aria-label="Energy in kilocalories"/>
                        </div>
                        <div>
                            <label>Protein (g)</label>
                            <input type="number" class="nutrient-input" aria-label="Protein in grams"/>
                        </div>
                        <div>
                            <label>Carbohydrate (g)</label>
                            <input type="number" class="nutrient-input" aria-label="Carbohydrates in grams"/>
                        </div>
                        <div>
                            <label>Fat (g)</label>
                            <input type="number" class="nutrient-input" aria-label="Fat in grams"/>
                        </div>
                        <div>
                            <label>Fiber (g)</label>
                            <input type="number" class="nutrient-input" aria-label="Fiber in grams"/>
                        </div>
                        <div>
                            <label>Fluid (ml)</label>
                            <input type="number" class="nutrient-input" aria-label="Fluid in milliliters"/>
                        </div>
                    </div>
                    <button type="button" class="add-item-button" onclick="addFoodItem('${mealType}')">Add Item</button>
                </form>
            `;

            // Append meal form to the container
            document.getElementById("meal-forms-container").appendChild(mealForm);
        }

        function addFoodItem(mealType) {
            const mealForm = document.getElementById(`${mealType}-form`);
            const foodEntriesContainer = mealForm.querySelector(".food-entries-container");

            const foodInput = mealForm.querySelector(".food-input").value;
            const nutrientInputs = mealForm.querySelectorAll(".nutrient-input");

            // Ensure at least food name is entered
            if (!foodInput.trim()) {
                alert("Please enter the food name.");
                return;
            }

            // Create a new food item entry
            const foodEntry = document.createElement("div");
            foodEntry.className = "food-entry";

            let nutrientData = `<strong>${foodInput}</strong> `;
            nutrientInputs.forEach((input, index) => {
                const label = input.parentElement.querySelector("label").innerText;
                const value = input.value;
                if (value.trim()) {
                    nutrientData += `<span>${label}: ${value}</span> `;
                }
                input.value = ""; // Clear input
            });

            foodEntry.innerHTML = nutrientData;
            foodEntriesContainer.appendChild(foodEntry);
        }
                // JavaScript to handle dynamic addition of medication and complementary/alternative medicine
                function addMedication() {
            const medicationsContainer = document.getElementById('medications-container');
            const inputDiv = document.createElement('div');
            inputDiv.classList.add('input-container');
            inputDiv.innerHTML = `
                <input type="text" name="medications[]" placeholder="Add used medication">
                <button type="button" onclick="removeItem(this)">&#x1F5D1;</button>
            `;
            medicationsContainer.appendChild(inputDiv);
        }

        function addCompMedicine() {
            const compMedsContainer = document.getElementById('comp-meds-container');
            const inputDiv = document.createElement('div');
            inputDiv.classList.add('input-container');
            inputDiv.innerHTML = `
                <input type="text" name="comp_meds[]" placeholder="Add complementary/alternative medication">
                <button type="button" onclick="removeItem(this)">&#x1F5D1;</button>
            `;
            compMedsContainer.appendChild(inputDiv);
        }

        // Remove a dynamic item
        function removeItem(button) {
            button.parentElement.remove();
        }
            // Initialize placeholders for calculations
            let energyRequirement = 0;
            let proteinRequirement = 0;
            let fatPercentage = 0;
            let choPercentage = 50.62; // Default CHO
            let choRecommendation = 0;
            let fatRecommendation = 0;

            // Handle form input updates dynamically
            function calculateEnergy() {
                const age = document.querySelector('[name="age"]').value;
                const gender = document.querySelector('[name="gender"]').value;
                const weight = document.querySelector('[name="weight"]').value;
                const height = document.querySelector('[name="height"]').value;
                const pal = document.querySelector('[name="pal"]').value;

                // Retrieve values from BMR equations when PAL is selected
                if (age && weight && height && pal) {
                    const bmr = calculateBMR(age, gender, weight, height);
                    energyRequirement = bmr * pal; // TDEE = BMR * PAL
                    proteinRequirement = calculateProtein(energyRequirement); // Constant protein requirement
                    choRecommendation = (energyRequirement * choPercentage) / 100; // CHO based on energy
                    fatRecommendation = (energyRequirement * fatPercentage) / 100; // Fat based on energy
                    
                    // Update dynamic fields with calculated values
                    document.querySelector('[name="energy"]').value = energyRequirement;
                    document.querySelector('[name="protein"]').value = proteinRequirement;
                    document.querySelector('[name="cho_recommendation"]').value = choRecommendation;
                    document.querySelector('[name="fat_recommendation"]').value = fatRecommendation;
                }
            }

            // Calculation for Basal Metabolic Rate (BMR) using Harris Benedict
            function calculateBMR(age, gender, weight, height) {
                if (gender === 'female') {
                    return 655 + (9.6 * weight) + (1.8 * height) - (4.7 * age); // Harris Benedict equation for female
                } else {
                    return 66 + (13.75 * weight) + (5 * height) - (6.75 * age); // Harris Benedict equation for male
                }
            }

            // Constant protein requirement calculation (fixed, 19.38%)
            function calculateProtein(energy) {
                return energy * 0.1938;  // Protein intake based on energy
            }

            // Trigger for PAL dropdown changes to dynamically calculate values
            function handlePALChange() {
                const palValue = document.querySelector('[name="activity_level"]').value;
                calculateEnergy(); // Recalculate when PAL is selected
            }

            // Helper function to create BMR formulas and table dynamically
            function createBMRTable() {
                const formulas = [
                    { name: "Harris Benedict Equation", formula: "BMR = 655 + (9.6 * weight) + (1.8 * height) - (4.7 * age)" },
                    { name: "Schofield Equation", formula: "BMR = 14.8 * weight + 487" },
                    { name: "Mifflin-St Jeor Equation", formula: "BMR = (10 * weight) + (6.25 * height) - (5 * age) + 5" }
                ];

                const table = document.createElement("table");
                table.innerHTML = `
                    <thead>
                        <tr>
                            <th>Formula</th>
                            <th>BMR (kcal)</th>
                            <th>TDEE (kcal)</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${formulas.map(formula => `
                            <tr>
                                <td>${formula.name}</td>
                                <td><input type="text" readonly name="bmr_${formula.name}" /></td>
                                <td><input type="text" readonly name="tdee_${formula.name}" /></td>
                            </tr>
                        `).join('')}
                    </tbody>
                `;
                document.querySelector('#dynamic-table').appendChild(table);
            }

            // Automatically called on page load
            document.addEventListener("DOMContentLoaded", function() {
                createBMRTable(); // Create the BMR table dynamically
                handlePALChange(); // Initialize based on selected activity level
            });

    </script>
<?php
}
    <form action="nutrition_assessment.php?section=E" method="POST">
        <div class="form-section">
            <label for="additional_notes">Additional Notes:</label>
            <textarea name="additional_notes"><?php echo isset($_SESSION['additional_notes']) ? $_SESSION['additional_notes'] : ''; ?></textarea><br>

            <input type="submit" value="Submit and Save Final Assessment">
        </div>
    </form>
}
?>
