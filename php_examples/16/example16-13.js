<script>
sports = ["Football", "Tennis", "Baseball"]
document.write("Start = "      + sports +  "<br />")
sports.push("Hockey");
document.write("After Push = " + sports +  "<br />")
removed = sports.pop()
document.write("After Pop = "  + sports +  "<br />")
document.write("Removed = "    + removed + "<br />")
</script>