# Design patterns

Named shapes for recurring object and module problems: the forces that make the shape worth it, and the cost. Skip the name when a function is enough.

This track assumes the learner can write a function, an interface, and a class. It does not require the algorithms track. Examples can use a hash map or a list once those data-structure lessons exist.

## Order

1. `when-to-name-a-pattern`
2. `strategy`
3. `state`
4. `observer`
5. `factory`
6. `builder`
7. `adapter`
8. `facade`
9. `decorator`
10. `singleton`
11. `command`
12. `composite`
13. `proxy`
14. `composition`

## Lessons

### 01. When to name a pattern

**Slug:** `when-to-name-a-pattern`  
**After:** none  
**Outcome:** Describe a pattern as a problem plus a structure, and decline the structure when the problem is not present.

A pattern is a recurring design that already has a name, so two people can talk about it quickly. It is not a goal. In an interview, say the force first ("ranking will change and the feed should not"), then the name ("that is a strategy"), then the cost ("one more type per algorithm"). If the variation is a single if, write the if. If you cannot name the force, you are decorating the code with classes.

Every later lesson has the same shape: force, structure, cost, and a small scenario.

**Practice:** given three one-screen designs, say which one is a pattern and which one is a function with a branch.  
**Artifacts:** guide; a step-by-step that accepts or rejects a proposed class hierarchy.

### 02. Strategy

**Slug:** `strategy`  
**After:** `when-to-name-a-pattern`  
**Outcome:** Swap an algorithm behind one interface without editing the caller.

Force: one job has several interchangeable procedures, and they will keep arriving (shipping cost by carrier, ranking a feed, compressing a payload). Structure: a caller holds an interface with one operation. Each procedure is a type that implements it. The caller receives the concrete type from the outside. Cost: a type per procedure, and the data each procedure needs must be on the interface or the constructor.

This is the default replacement for a growing switch on a type code. Template method is the inheritance cousin: a base class runs the skeleton and subclasses override steps. Prefer strategy when the variation is the whole algorithm and you want to keep the caller free of a base class.

**Practice:** price a shipment with two carriers behind one `quote(parcel)` interface. Add a third carrier without editing the first two.  
**Artifacts:** guide; a game that plugs a different quote function into a fixed checkout; a step-by-step that chooses strategy vs a switch vs template method.

### 03. State

**Slug:** `state`  
**After:** `strategy`  
**Outcome:** Let an object change behavior when its mode changes, with transitions written in one place.

Force: behavior depends on the mode (draft, published, archived; a TCP connection; a document checkout), and scattered ifs on a status enum drift out of date. Structure: a context holds a state object. Each state implements the same actions and returns the next state. Illegal actions live in the state that should refuse them. Cost: one type per mode, which is worth it when transitions are the bug farm, and noisy when there are two modes and one transition.

State and strategy share a structure. The difference is who changes the implementation. A strategy is chosen from outside and usually stays. A state replaces itself as the object lives.

**Practice:** a document with draft, in review, and published. Say which edits each mode allows, and where the transition to published is coded.  
**Artifacts:** guide; a game that presses an action and shows the mode that accepts or refuses it.

### 04. Observer

**Slug:** `observer`  
**After:** `when-to-name-a-pattern`  
**Outcome:** Notify dependents when a subject changes, and name the leak and the order problems.

Force: several parts must react to one change, and the subject should not know them by concrete type (a model and three widgets, an order and the email sender, the warehouse, the invoice). Structure: the subject keeps a list of observers and calls them on change. Observers register and unregister. Cost: the subject does not know who is listening, so a forgotten unregister leaks, order of notification is easy to rely on and wrong to rely on, and a slow observer blocks the rest unless you move the call off the thread.

Pub/sub is the same force with a broker between subject and observers, so they do not share a process. Use the design-systems queue lesson when that broker must survive a crash. Inside one process, a list of listeners is enough.

**Practice:** a counter with two listeners. Unsubscribe one and show that it stops firing.  
**Artifacts:** guide; a game that registers and forgets to unregister; a step-by-step that picks direct observers vs a queue.

### 05. Factory

**Slug:** `factory`  
**After:** `strategy`  
**Outcome:** Hide which concrete type gets constructed, in one place the caller can find.

Force: the caller knows the job ("a logger", "a notifier") and should not know the concrete class, the constructor arguments, or the feature flag that picks it. Structure: a function or type whose job is construction. A simple factory is one function with a branch. A factory method is a subclass that overrides the creation step. An abstract factory builds a family of objects that belong together (UI widgets for one platform). Cost: another type, and a branch that still exists, only now it lives in one file.

Use the simplest of the three that matches the force. A single `createNotifier(kind)` function is the whole pattern for most codebases. Reach for an abstract factory when two products must match (a button and a dialog from the same platform) and constructing them separately would mix families.

**Practice:** construct a test payment client or a live one from configuration, without the checkout code importing either class.  
**Artifacts:** guide; a step-by-step that picks a function, a factory method, or an abstract factory.

### 06. Builder

**Slug:** `builder`  
**After:** `factory`  
**Outcome:** Construct one object with many optional parts, and refuse a half-built object from escaping.

Force: a constructor has too many parameters, most of them optional, and some combinations are illegal (a query with neither filter nor id, a request with conflicting flags). Structure: a builder collects steps and a final `build()` checks the combination and returns the object. The object itself has no setters for those fields. Cost: a second type, and a temptation to use it for objects with two fields.

Factory chooses which class. Builder assembles one class. They stack when you both pick a class and fill many fields. Telescoping constructors and a heap of optional arguments are the smell that builder is aimed at.

**Practice:** build a search query that requires a tenant id and at least one filter, and fails in `build()` otherwise.  
**Artifacts:** guide; a game that enables Build only when the required steps are present.

### 07. Adapter

**Slug:** `adapter`  
**After:** `when-to-name-a-pattern`  
**Outcome:** Wrap a type you do not control so a caller can use the interface it already has.

Force: a caller speaks interface A, a library speaks interface B, and you cannot change either. Structure: an adapter implements A and holds a B, translating each call. Cost: a thin type that can hide a lossy translation. Write down what you could not map.

Do not use an adapter to avoid touching code you do own. Change that code. The pattern earns its keep at a boundary: a third-party SDK, a legacy module, a second implementation you are migrating toward.

**Practice:** a logger interface with `info(msg)`, and a vendor client with `log(level, msg, fields)`. One adapter, and one field you refuse to drop silently.  
**Artifacts:** guide; a step-by-step that adapts or edits the caller, depending on who owns the type.

### 08. Facade

**Slug:** `facade`  
**After:** `adapter`  
**Outcome:** Offer one simple operation in front of a subsystem the caller should not orchestrate.

Force: a real task takes five calls in the right order across several types (start a checkout, reserve stock, charge, send receipt), and most callers get the order wrong. Structure: one type with one or two methods that run the sequence. The subsystem types stay available for the few callers that need them. Cost: a facade that grows into a second system, and a hiding place for errors if it swallows failures from the inner calls.

Adapter changes the shape of one type. Facade reduces the number of types a caller must touch. A facade may use adapters inside it.

**Practice:** `placeOrder` as a facade over inventory, payment, and email. Say which inner error the caller still has to see.  
**Artifacts:** guide; a step-by-step that adds a facade or leaves the calls at the caller because there is only one caller.

### 09. Decorator

**Slug:** `decorator`  
**After:** `strategy`  
**Outcome:** Add behavior to an object by wrapping it, keeping the same interface the caller already uses.

Force: you want extra behavior (timing, retry, compression, a cache) on some objects and not others, and subclasses would explode into every combination. Structure: a decorator implements the same interface, holds the next object, and adds its bit before or after the delegated call. Wrappers stack. Cost: identity gets confusing (`wrapped` is not the inner object), and order of wrapping changes the result (cache outside retry is not cache inside retry).

Decorator adds behavior around one object. Strategy replaces the behavior. If the caller must choose a pipeline of add-ons at runtime, stack decorators. If the caller must choose one algorithm, pass a strategy.

**Practice:** a repository interface, a retry decorator, and a timing decorator. Swap their order and say which failures get timed.  
**Artifacts:** guide; a game that stacks wrappers and shows the call order.

### 10. Singleton

**Slug:** `singleton`  
**After:** `factory`  
**Outcome:** Recognize a single shared instance, and prefer passing that instance in over hiding it.

Force: some resource genuinely exists once per process (a connection pool, a configuration snapshot, a metrics registry). Structure in the classic pattern: a private constructor and a static accessor that creates the instance on first use. Cost: hidden dependency. Tests cannot substitute it without contortions. Order of initialization becomes global. Two reasons to call `getInstance` look the same and are not.

The usual interview answer is to create the one instance at the edge of the program and pass it into the objects that need it. You still have one instance. You no longer have a global lookup. Mention double-checked locking only to say that a language's standard lazy holder or a startup constructor is safer than hand-rolled locks.

**Practice:** a metrics registry used by three services. Show the hidden `getInstance` version and the constructor-argument version.  
**Artifacts:** guide; a step-by-step that rejects a singleton and passes the dependency, or keeps one when the object is the process itself.

### 11. Command

**Slug:** `command`  
**After:** `when-to-name-a-pattern`, data-structures `stacks`  
**Outcome:** Represent a request as an object so you can queue it, log it, or undo it.

Force: the moment you decide to do work is not the moment you run it, or you must reverse it later (editor operations, a job queue in memory, a macro). Structure: a command object has `execute` and, when you need undo, `undo` plus the data to reverse. An invoker stores commands. A stack of executed commands is the undo history. Cost: a type per action, and undo state that is easy to get wrong if the command is not the only writer.

A queue of commands inside a process is this pattern. A durable job queue is the design-systems queue lesson. Say which one you are in.

**Practice:** an editor with insert and delete, undo twice, redo once.  
**Artifacts:** guide; a game that pushes commands onto an undo stack and pops them.

### 12. Composite

**Slug:** `composite`  
**After:** `decorator`, data-structures `trees`  
**Outcome:** Treat a single item and a group of items through one interface.

Force: a tree of parts and wholes (a UI of panels and buttons, a file system of files and folders, a price of line items and bundles) and the caller should not care which it holds. Structure: a component interface, a leaf, and a composite that holds children and implements the interface by delegating. Cost: operations that make sense on a leaf and not on a group, or the reverse. Put those differences in the interface on purpose, or the caller will downcast and the pattern is gone.

Iterator pairs with this. Walking the tree should not expose whether the current node stores an array or a map of children. A simple recursive method is enough until the walk itself needs to pause and resume.

**Practice:** a price tree. A leaf returns its price. A bundle returns the sum of its children. Discount wraps a component (that wrap is the decorator).  
**Artifacts:** guide; a game that adds a leaf under a selected node and shows the total change.

### 13. Proxy

**Slug:** `proxy`  
**After:** `decorator`  
**Outcome:** Put a stand-in in front of an object to control access, and say what the stand-in checks.

Force: the caller should use the real interface, but something must happen first: a permission check, a lazy load, a remote call, a cache lookup. Structure: a proxy implements the same interface, holds or creates the real object, and decides whether to forward. Cost: the caller cannot tell the proxy from the real object, which is the point and also how a hidden network call ends up inside a getter.

Decorator adds behavior and still forwards. Proxy controls whether the real object is reached at all. A virtual proxy creates the expensive object on first use. A protection proxy checks auth. A remote proxy hides a network boundary, which means it also hides the failure lesson from the design-systems track: timeouts still apply.

**Practice:** a document that loads its body on first `read()`, and a second proxy that refuses `read()` without a role. Stack them and say which one is outer.  
**Artifacts:** guide; a step-by-step that chooses proxy vs decorator from "control access" vs "add behavior".

### 14. Composition

**Slug:** `composition`  
**After:** `strategy`, `decorator`, `singleton`  
**Outcome:** Assemble behavior by holding objects, and use inheritance only when the type truly is a subtype.

Force: inheritance was used to reuse code, and subclasses now inherit methods they must not have, or override methods in ways the base class cannot tolerate. Structure: a small interface, an object that holds other objects and forwards, and dependencies passed in constructors (the singleton lesson). Strategy, decorator, and proxy are this idea with specific forces. Cost: more objects, and a wiring site that must exist at startup. That wiring site is a feature. It is where tests pass fakes.

Prefer composition when you can say "has a". Use inheritance when you can say "is a" and the subclass honors the base contract, including preconditions and postconditions. A square that breaks a rectangle's independent width and height is the classic failure of "is a".

**Practice:** rewrite a three-level inheritance of notifiers (base, email, email-with-retry) into a notifier interface, an email sender, and a retry decorator.  
**Artifacts:** guide; a step-by-step that chooses holds-a or is-a for a given pair of types. The wrong path inherits to reuse one method.
