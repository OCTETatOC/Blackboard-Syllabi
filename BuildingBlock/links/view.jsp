<html lang="en">

<%@ page import="java.util.*,
                java.lang.Integer,
                blackboard.base.*,
                blackboard.data.*,
                blackboard.data.user.*,
                blackboard.data.course.*,
                blackboard.persist.*,
                blackboard.persist.user.*,
                blackboard.persist.course.*,
                blackboard.platform.*,
                blackboard.platform.persistence.*,
                blackboard.portal.external.*,
                blackboard.platform.session.*"
%>
<%@ taglib uri="/bbData" prefix="bbData"%>
<%@ taglib uri="/bbUI" prefix="bbUI"%>
<%@ page import="blackboard.platform.plugin.PlugInUtil"%>

<bbData:context id="ctx">

<%
    BbPersistenceManager persistenceManager = BbServiceManager.getPersistenceService().getDbPersistenceManager();
    CourseDbLoader courseLoader = (CourseDbLoader)persistenceManager.getLoader(CourseDbLoader.TYPE);
    User currentUser = ctx.getUser();

    Set<Course> taughtCourses = new HashSet<Course>();
    taughtCourses.addAll(courseLoader.loadByUserIdAndCourseMembershipRole(currentUser.getId(), CourseMembership.Role.INSTRUCTOR));

    if(taughtCourses.isEmpty())
    {
        out.println("<div>You do not appear to be teaching any courses at the moment.</div>");
    }
    else
    { %>
        <br />
        <form action="https://conevals.csr.oberlin.edu/syllabi_testing/syllabi.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="MAX_FILE_SIZE" value="100000">
        <input type="hidden" name="username" value="<%=currentUser.getUserName()%>">
        <%
        for(Course taughtCourse : taughtCourses)
        {
            String courseId = taughtCourse.getCourseId();
            %>
            <div><b><%=taughtCourse.getTitle()%></b></div>
            <input type="hidden" name="courses[]" value="<%=taughtCourse.getCourseId()%>">
            <input type="file" name="<%=courseId%>"><br />
            <br />
            <input type="radio" name="public_<%=courseId%>" value="true"><b>Allow</b> students not registered for the course to see this syllabus.<br />
            <input type="radio" name="public_<%=courseId%>" value="false" checked><b>Do not allow</b> students not registered for the course to see this syllabus.<br />
            <br />
            <br />
        <% }
        %>
        <input type="submit" value="Upload file(s)">
        <br />
        </form>
        <%
    }
%>

</bbData:context>
</html>
